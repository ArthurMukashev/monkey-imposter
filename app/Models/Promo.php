<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'placement', 'priority', 'section', 'active_from', 'active_until',
        'title', 'teaser', 'target_type', 'target_slug', 'target_url'
    ];

    protected $casts = [
        'placement' => 'string',
        'section' => 'string',
        'target_type' => 'string',
        'active_from' => 'datetime',
        'active_until' => 'datetime',
    ];

    // Полиморфное изображение (одно)
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    // Скоуп для активных промо
    public function scopeActive($query)
    {
        $now = now();
        return $query->where(function ($q) use ($now) {
            $q->where('active_from', '<=', $now)
                ->where(function ($q2) use ($now) {
                    $q2->whereNull('active_until')
                        ->orWhere('active_until', '>=', $now);
                });
        });
    }

    public function getImageData()
    {
        if ($this->image) {
            return [
                'url' => $this->image->url,
                'alt' => $this->image->alt,
                'title' => $this->image->title,
                'isCover' => true,
            ];
        }
        return null;
    }
}
