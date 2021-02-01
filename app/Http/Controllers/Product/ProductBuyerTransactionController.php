<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $rules = [ 'quantity' => 'required|integer|min:1' ]; // TODO: Reglas de validacción

        $this->validate( $request, $rules );

        if ( $buyer->id == $product->seller_id ) {
            return $this->errorResponse( 'El comprardor debe  ser diferente al vendedor', 409 );
        }

        if ( !$buyer->isVerified() ) {
            return $this->errorResponse( 'El comprador debe ser un usuario vereficado', 409 );
        }

        if ( !$product->seller->isVerified() ) {
            return $this->errorResponse( 'El vendedor debe ser un usuario vereficado', 409 );
        }

        if ( !$product->available() ) {
            return $this->errorResponse( 'El producto no está disponoble', 409 );
        }

        if ( $product->quantity < $request->quantity ) {
            return $this->errorResponse( 'El producto no cuenta con la cantidad requerida para la transacción', 409 );
        }

        // TODO: Crear una transacción
        return DB::transaction( function () use ( $request, $product, $buyer ) {
            $product->quantity -= $request->quantity;
            $product->save();

            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id
            ]);

            return $this->showOne( $transaction, 201 );
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
