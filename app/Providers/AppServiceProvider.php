<?php

namespace App\Providers;

use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
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
        User::created( function( $user ) { // TODO: created: para enviar un correo
            retry( 5, function() use ( $user ) { // TODO: retry: Intenta reenviar los correos en caso de fallo
                Mail::to( $user )->send( new UserCreated( $user ) );
            }, 100);
        });

        User::updated( function( $user ) { // TODO: updated: Enviar correo electrónico si cambia la dirección
            if ( $user->isDirty( 'email' ) ) { // solo se envia si el correo cambia
                retry( 5, function() use ( $user ) {
                    Mail::to( $user )->send( new UserMailChanged( $user ) );
                }, 100);
            }
        });

        Product::updated( function( $product ) { // TODO: updated: para cambiar el estado de un producto
            if ( $product->quantity == 0 && $product->available() ) {
                $product->status = Product::PRODUCT_NOT_AVAILABLE;

                $product->save();
            }
        });
    }
}
