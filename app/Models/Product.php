<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['brand_id', 'product_code', 'combination'];


    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function attributeValues()
    {
        return $this->hasManyThrough(AttributeValue::class, Attribute::class, 'brand_id', 'attribute_id');
    }



    public function getAttributeDefinitions()
    {
        $combination = json_decode($this->combination, true);
        $definitions = [];

        foreach ($combination as $attributeName => $valueCode) {
            $attribute = Attribute::where('name', $attributeName)->first();

            if ($attribute) {
                $value = AttributeValue::where('attribute_id', $attribute->id)
                    ->where('value', $valueCode)
                    ->first();

                if ($value) {
                    $definitions[$attributeName] = $value->description;
                }
            }
        }

        return $definitions;
    }
}


