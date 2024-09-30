<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $guarded = ['id'];


    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($brand) {
            $brand->attributes()->each(function ($attribute) {
                $attribute->delete();
            });
            $brand->products()->each(function ($product) {
                $product->delete();
            });
        });
    }
}


