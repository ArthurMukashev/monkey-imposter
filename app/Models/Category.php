<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'slug', 'section', 'title', 'short_description', 'sort_order'
    ];

    protected $casts = [
        'section' => 'string',
    ];

    public function places()
    {
        return $this->hasMany(Place::class);
    }
}
