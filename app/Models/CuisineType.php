<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuisineType extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['slug', 'title'];

    public function places()
    {
        return $this->belongsToMany(Place::class, 'place_cuisine_type');
    }
}
