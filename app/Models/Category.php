<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Category extends Model
{
    protected $guarded = ['id'];

    public function parent(){
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get categories for select inputs without lazy-loading each ancestor.
     *
     * All categories are loaded in one query, then their already-loaded parent
     * relation is connected from the in-memory collection. This lets the
     * full_name accessor walk an arbitrarily deep tree without extra queries.
     *
     * @return Collection<int, static>
     */
    public static function forSelection(): Collection
    {
        $categories = static::query()
            ->select(['id', 'name', 'parent_id'])
            ->orderBy('name')
            ->get();

        $categoriesById = $categories->keyBy('id');

        $categories->each(fn (self $category) => $category->setRelation(
            'parent',
            $categoriesById->get($category->parent_id),
        ));

        return $categories;
    }

    /**
     * Get selectable categories while excluding a category and its descendants.
     *
     * @return Collection<int, static>
     */
    public static function forSelectionExceptSubtree(self $category): Collection
    {
        $categories = static::forSelection();
        $excludedIds = [$category->getKey()];
        $parentIds = $excludedIds;

        while ($parentIds !== []) {
            $parentIds = $categories->whereIn('parent_id', $parentIds)
                ->pluck('id')
                ->reject(fn ($id) => in_array($id, $excludedIds, true))
                ->all();
            $excludedIds = [...$excludedIds, ...$parentIds];
        }

        return $categories->reject(
            fn (self $candidate) => in_array($candidate->getKey(), $excludedIds, true),
        );
    }

    public function getFullNameAttribute(): string
    {
        $names = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($names, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' › ', $names);
    }

    public function children(){
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function posts(){
        return $this->belongsToMany(Post::class);
    }

    public function descendants(){
        $descendants = collect();

        foreach($this->children as $child){
            $descendants->push($child);

            $descendants = $descendants->merge($child->descendants());
        }
        return $descendants;
    }

    public function isValidParent(Category $parent): bool{
        return !$this->descendants()->pluck('id')->push($this->id)->contains($parent->id);
    }
}
