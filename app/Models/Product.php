<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    const PRODUCT_AVAILABLE = 'Disponible';
    const PRODUCT_NOT_AVAILABLE = 'No disponible';

    protected $dates = [ 'deleted_at' ];

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'status',
        'image',
        'seller_id',
    ];

    protected $hidden = [ 'pivot' ];

    public function available() {
        return $this->status == Product::PRODUCT_AVAILABLE;
    }

    public function categories() {
        return $this->belongsToMany( Category::class );
    }

    public function seller() {
        return $this->belongsTo( Seller::class );
    }

    public function transactions() {
        return $this->hasMany( Transaction::class );
    }
}
