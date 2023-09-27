<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    public function product_taxes() {
        return $this->hasMany(ProductTax::class);
    }
    public function category_taxes() {
        return $this->hasMany(CategoryTax::class);
    }
}
