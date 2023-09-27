<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryTax extends Model
{
    //
    public function tax_rec()
    {
        return $this->belongsTo(Tax::class,'tax_id','id');
    }

}
