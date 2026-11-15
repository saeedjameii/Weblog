<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ProductAiComparisonService
{
    protected string $apiKey;
    protected string $model;
    protected ?string $proxy;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.api_key');
        $this->model  = (string) config('services.gemini.model', 'gemini-1.5-flash');
        $this->proxy  = config('services.gemini.proxy');
    }

    public function compareProducts(Collection $products, bool $withWebSearch = false): array
    {
        $ids = $products->pluck('id')->sort()->implode('_');
        $mode = $withWebSearch ? 'deep_web' : 'quick';
        $cacheKey = "ai_compare_{$mode}_{$ids}";

        return Cache::remember($cacheKey, now()->addHours(48), function () use ($products, $withWebSearch) {
            $formattedData = $this->prepareProductsData($products);
            return $this->callGemini($formattedData, $withWebSearch);
        });
    }

    protected function prepareProductsData(Collection $products): array
    {
        return $products->map(function ($product) {
            $minPrice = $product->prices->sortBy('final_price')->first();

            $attributes = $product->prices
                ->flatMap(fn ($p) => $p->attributeValues)
                ->map(fn ($av) => ($av->attribute?->name ?? 'ویژگی') . ': ' . $av->value)
                ->unique()
                ->values()
                ->all();

            return [
                'id'          => $product->id,
                'name'        => $product->name,
                'price'       => $minPrice ? number_format((float) $minPrice->final_price) . ' تومان' : 'نامشخص',
                'weight'      => filled($product->weight) ? $product->weight . ' ' . $product->unit : null,
                'attributes'  => $attributes,
                'description' => strip_tags($product->description ?? ''),
            ];
        })->toArray();
    }

    protected function callGemini(array $productsData, bool $withWebSearch): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('کلید سرویس هوش مصنوعی (GEMINI_API_KEY) تنظیم نشده است.');
        }

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $systemInstruction = "شما یک مشاور و کارشناس حرفه‌ای خرید کالا هستید. وظیفه شما مقایسه دقیق و بی‌طرفانه محصولات داده‌شده به زبان فارسی است.";
        
        $userPrompt = "محصولات زیر را با دقت بررسی و مقایسه کنید:\n" . json_encode($productsData, JSON_UNESCAPED_UNICODE) . "\n\n";

        if ($withWebSearch) {
            $userPrompt .= "📌 حالت تحلیل عمیق و کارشناسی بازار فعال است: لطفاً فراتر از مشخصات خام جدول، با تکیه بر نقد و بررسی‌های معتبر، تست‌های بنچمارک سخت‌افزاری، گزارش‌های کیفی و نظرات و تجربیات خریداران در سطح وب، نقاط قوت و ضعف پنهان، دوام، کیفیت ساخت و ارزش خرید واقعی هر محصول را با دقت بالا تحلیل و مقایسه کن.\n";
        }

        $userPrompt .= "پاسخ را الزماً و دقیقاً در قالب فرمت JSON معتبر زیر ارسال کن (بدون هیچ کلمه اضافی قبل یا بعد از آن):
        {
            \"summary\": \"خلاصه مقایسه در ۲ یا ۳ جمله\",
            \"verdict\": \"توصیه نهایی برای خریدار (کدام برای چه سلیقه یا بودجه‌ای مناسب‌تر است)\",
            \"products\": [
                {
                    \"id\": 123,
                    \"name\": \"نام دقیق محصول\",
                    \"best_for\": \"مناسب برای چه کسانی است؟\",
                    \"pros\": [\"مزیت ۱\", \"مزیت ۲\"],
                    \"cons\": [\"عیب یا نقطه ضعف ۱\"]
                }
            ]
        }";

        $payload = [
            'contents' => [
                ['parts' => [['text' => $systemInstruction . "\n\n" . $userPrompt]]]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
                'temperature' => $withWebSearch ? 0.6 : 0.3,
            ]
        ];

        $response = $this->executeApiCall($endpoint, $payload);

        // در صورت خطای شلوغی سرور (503) یا محدودیت موقت، سوییچ خودکار به مدل پشتیبان
        if (!$response->successful() && in_array($response->status(), [503, 429])) {
            $fallbackModel = ($this->model === 'gemini-3.5-flash') ? 'gemini-3.5-flash-lite' : 'gemini-3.5-flash';
            $fallbackEndpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$fallbackModel}:generateContent?key={$this->apiKey}";
            $response = $this->executeApiCall($fallbackEndpoint, $payload);
        }

        if (!$response->successful()) {
            \Log::error('Gemini API Error: ' . $response->body());
            throw new \Exception('خطا در دریافت پاسخ از هوش مصنوعی: ' . $response->status());
        }

        $rawText = $response->json('candidates.0.content.parts.0.text') ?? '{}';
        
        $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($rawText));
        $decoded = json_decode($cleanJson, true);

        return is_array($decoded) ? $decoded : ['summary' => $rawText, 'products' => []];
    }

    protected function executeApiCall(string $endpoint, array $payload)
    {
        $httpClient = Http::timeout(45)->retry(2, 400);

        if (!empty($this->proxy)) {
            $httpClient = $httpClient->withOptions(['proxy' => $this->proxy]);
        }

        return $httpClient->post($endpoint, $payload);
    }
}