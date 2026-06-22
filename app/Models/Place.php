<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'slug', 'section', 'title', 'short_description', 'description_html',
        'category_id', 'city_id', 'latitude', 'longitude', 'address',
        'working_hours', 'average_bill', 'menu_html', 'schedule',
        'seo_title', 'seo_description', 'seo_canonical_path', 'is_published'
    ];

    protected $casts = [
        'section' => 'string',
        'schedule' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'is_published' => 'boolean',
    ];

    // Отношения
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'place_tag');
    }

    public function cuisineTypes()
    {
        return $this->belongsToMany(CuisineType::class, 'place_cuisine_type');
    }

    // Полиморфные изображения (много)
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // Обложка (удобный метод)
    public function coverImage()
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_cover', true);
    }

    // Скоупы для фильтрации
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeSection($query, $section)
    {
        return $query->where('section', $section);
    }

    public function getCoverImageAttribute()
    {
        $cover = $this->images->firstWhere('is_cover', true);
        if ($cover) {
            return [
                'url' => $cover->url,
                'alt' => $cover->alt,
                'title' => $cover->title,
                'isCover' => true,
            ];
        }
        return null;
    }
}
