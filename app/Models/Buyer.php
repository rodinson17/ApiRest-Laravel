<?php

namespace App\Models;

use App\Scopes\BuyerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyer extends User
{
    use HasFactory;

    protected static function booted() {  // TODO: Using Global Scopes
        static::addGlobalScope(new BuyerScope);
    }

    public function transactions() {
        return $this->hasMany( Transaction::class );
    }
}