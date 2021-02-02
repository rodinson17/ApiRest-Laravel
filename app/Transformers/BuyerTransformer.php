<?php

namespace App\Transformers;

use App\Models\Buyer;
use League\Fractal\TransformerAbstract;

class BuyerTransformer extends TransformerAbstract
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
    public function transform( Buyer $buyer )
    {
        return [
            'identifidor' => (int)$buyer->id,
            'nombre' => (string)$buyer->name,
            'correo' => (string)$buyer->email,
            'esVerificado' => (int)$buyer->verified,
            'fechaCreacion' => (string)$buyer->created_at,
            'fechaActualizacion' => (string)$buyer->updated_at,
            'fechaEliminacion' => isset( $buyer->deleted_at ) ? (string)$buyer->deleted_at : null,
            'links' => [
                [ 'rel' => 'self', 'href' => route( 'buyers.show', $buyer->id ) ],
                [ 'rel' => 'buyer.categories', 'href' => route( 'buyers.categories.index', $buyer->id ) ],
                [ 'rel' => 'buyer.products', 'href' => route( 'buyers.products.index', $buyer->id ) ],
                [ 'rel' => 'buyer.sellers', 'href' => route( 'buyers.sellers.index', $buyer->id ) ],
                [ 'rel' => 'buyer.transactions', 'href' => route( 'buyers.transactions.index', $buyer->id ) ],
                [ 'rel' => 'user', 'href' => route( 'users.show', $buyer->id ) ],
            ],
        ];
    }

    public static function originalAttributes( $index )
    {
        $attributes = [
            'identifidor' => 'id',
            'nombre' => 'name',
            'correo' => 'email',
            'esVerificado' => 'verified',
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
            'name' => 'nombre',
            'email' => 'correo',
            'verified' => 'esVerificado',
            'created_at' => 'fechaCreacion',
            'updated_at' => 'fechaActualizacion',
            'deleted_at' => 'fechaEliminacion',
        ];

        return isset( $attributes[$index]) ? $attributes[$index] : null;
    }
}
