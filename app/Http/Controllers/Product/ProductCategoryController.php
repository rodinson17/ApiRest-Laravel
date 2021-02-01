<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $listCategories = $product->categories;

        return $this->showAll( $listCategories );
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
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, Product $product, Category $category)
    {
        // sync, attach, syncWithoutDetaching
        //$product->categories()->sync([ $category->id ]); // TODO: sync: Borra todo lo anterior y deja un solo dato
        //$product->categories()->attach([ $category->id ]);  // TODO: attch: Permite que se repitan los datos
        $product->categories()->syncWithoutDetaching([ $category->id ]); // TODO: syncWithoutDetaching: Permite agregar un nuevo dato sin que se repitan los anteriores datos

        return $this->showAll( $product->categories );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Category $category)
    {
        if ( !$product->categories()->find( $category->id ) ) {
            return $this->errorResponse( 'La categoría especificada no es una categoría de este producto', 404 );
        }

        $product->categories()->detach([ $category->id ]);

        return $this->showAll( $product->categories );
    }
}
