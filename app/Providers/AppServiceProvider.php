<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Product::updated( function( $product ) { // TODO: updated: para cambiar el estado de un producto
            if ( $product->quantity == 0 && $product->available() ) {
                $product->status = Product::PRODUCT_NOT_AVAILABLE;

                $product->save();
            }
        });
    }
}
