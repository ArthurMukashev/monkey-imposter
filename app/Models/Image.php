<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'url', 'alt', 'title', 'is_cover', 'sort_order',
        'imageable_id', 'imageable_type'
    ];

    protected $casts = [
        'is_cover' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Полиморфная связь
    public function imageable()
    {
        return $this->morphTo();
    }
}
