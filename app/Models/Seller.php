<?php

namespace App\Models;

use App\Scopes\SellerScope;
use App\Transformers\SellerTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends User
{
    use HasFactory;

    public $transformer = SellerTransformer::class;

    protected static function booted() {  // TODO: Using Global Scopes
        static::addGlobalScope(new SellerScope);
    }

    public function products() {
        return $this->hasMany( Product::class );
    }
}
