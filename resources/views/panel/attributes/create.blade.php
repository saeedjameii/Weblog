@extends('layout.panel')

@section('title', 'ایجاد ویژگی')
@section('header-actions')<a class="browse-link" href="{{ route('panel.attributes.index') }}">بازگشت به ویژگی‌ها</a>@endsection

@section('content')
<main class="page-area"><div class="main-content">
    <section class="intro"><p class="kicker">مدیریت فروشگاه</p><h1 class="display-font">ایجاد ویژگی</h1><p class="intro-copy">ابتدا ویژگی را ثبت کنید؛ سپس مقدارهای آن را در صفحهٔ ویرایش اضافه کنید.</p></section>
    <div class="workspace workspace--single"><form action="{{ route('panel.attributes.store') }}" method="POST" class="form-card">@csrf
        @if ($errors->any())<div class="alert alert-danger"><ul class="errors-list">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="field-grid">
            <div class="field full-field"><label for="attribute-name">نام ویژگی</label><input class="form-control" id="attribute-name" name="name" maxlength="100" value="{{ old('name') }}" required>@error('name')<span class="validation-message">{{ $message }}</span>@enderror</div>
            <div class="field full-field"><label for="attribute-type">نوع ویژگی</label><select class="form-control" id="attribute-type" name="type" required><option value="text" @selected(old('type', 'text') === 'text')>متنی</option><option value="color" @selected(old('type') === 'color')>رنگ</option></select>@error('type')<span class="validation-message">{{ $message }}</span>@enderror</div>
        </div>
        <div class="actions"><a class="button button-secondary" href="{{ route('panel.attributes.index') }}">انصراف</a><button class="button button-primary" type="submit">ثبت ویژگی</button></div>
    </form></div>
</div></main>
@endsection
