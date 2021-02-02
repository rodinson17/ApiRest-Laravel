<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform( Product $product )
    {
        return [
            'identifidor' => (int)$product->id,
            'titulo' => (string)$product->name,
            'detalles' => (string)$product->description,
            'disponibles' => (string)$product->quantity,
            'estado' => (string)$product->status,
            'imagen' => url( "img/{$product->image}" ),
            'vendedor' => (int)$product->seller_id,
            'fechaCreacion' => (string)$product->created_at,
            'fechaActualizacion' => (string)$product->updated_at,
            'fechaEliminacion' => isset( $product->deleted_at ) ? (string)$product->deleted_at : null,
            'links' => [
                [ 'rel' => 'self', 'href' => route( 'products.show', $product->id ) ],
                [ 'rel' => 'product.buyers', 'href' => route( 'products.buyers.index', $product->id ) ],
                [ 'rel' => 'product.categories', 'href' => route( 'products.categories.index', $product->id ) ],
                [ 'rel' => 'product.transactions', 'href' => route( 'products.transactions.index', $product->id ) ],
                [ 'rel' => 'seller', 'href' => route( 'sellers.show', $product->seller_id ) ],
            ],
        ];
    }

    public static function originalAttributes( $index )
    {
        $attributes = [
            'identifidor' => 'id',
            'titulo' => 'name',
            'detalles' => 'description',
            'disponibles' => 'quantity',
            'estado' => 'status',
            'imagen' => 'image',
            'vendedor' => 'seller_id',
            'fechaCreacion' => 'created_at',
            'fechaActualizacion' => 'updated_at',
            'fechaEliminacion' => 'deleted_at',
        ];

        return isset( $attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttributes( $index )
    {
        $attributes = [
            'id' => 'identifidor',
            'name' => 'titulo',
            'description' => 'detalles',
            'quantity' => 'disponibles',
            'status' => 'estado',
            'image' => 'imagen',
            'seller_id' => 'vendedor',
            'created_at' => 'fechaCreacion',
            'updated_at' => 'fechaActualizacion',
            'deleted_at' => 'fechaEliminacion',
        ];

        return isset( $attributes[$index]) ? $attributes[$index] : null;
    }
}
