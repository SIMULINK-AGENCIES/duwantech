<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'category_id', 'price', 'image', 'gallery', 'description', 'attributes', 'views',
        'is_featured', 'is_active'
    ];

    protected $casts = [
        'attributes' => 'array',
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_featured' => false,
        'is_active' => true,
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
