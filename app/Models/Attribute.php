<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $guarded = ['id'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    protected static function boot()
    {
        parent::boot();


        static::deleting(function ($attribute) {
            $attribute->values()->each(function ($value) {
                $value->delete();
            });
        });
    }
}

