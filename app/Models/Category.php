<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];

    public function parent(){
        return $this->belongsTo(Category::class, 'parent_id');
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
