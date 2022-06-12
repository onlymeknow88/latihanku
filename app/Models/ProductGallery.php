<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductGallery extends Model
{
    use HasFactory;


    protected $fillable = [
        'products_id',
        'url',
        'path'
    ];

    public function getUrlAttribute($url)
    {
        return config('app_url') . Storage::url($url);
    }
}
