<?php

namespace App\Traits;

//use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection; // TODO: Se recomienda usar esta importación

trait ApiResponser { // TODO: Metodos response json

    private function successResponse( $data, $code ) {
        return response()->json( $data, $code );
    }

    protected function errorResponse( $message, $code ) {
        return response()->json( [ 'error' => $message, 'code' => $code ], $code );
    }

    protected function showAll( Collection $collection, $code = 200 ) {
        if ( $collection->isEmpty() ) {
            return $this->successResponse( ['data' => $collection ], $code );
        }

        $transformer = $collection->first()->transformer;

        $collection = $this->filterData( $collection, $transformer );
        $collection = $this->sortData( $collection, $transformer );
        $collection = $this->transformData( $collection, $transformer );

        //return $this->successResponse( [ 'data' => $collection ], $code );
        return $this->successResponse( $collection, $code );
    }

    protected function showOne( Model $instance, $code = 200 ) {
        //return $this->successResponse( [ 'data' => $instance ], $code );
        return $this->successResponse( $instance, $code );
    }

    protected function showMessage( $message, $code = 200 ) {
        return $this->successResponse( [ 'data' => $message ], $code );
    }

    protected function sortData( Collection $collection, $transformer ) {
        if ( request()->has( 'sort_by' ) ) {
            $attribute = $transformer::originalAttributes( request()->sort_by );

            $collection = $collection->sortBy->{$attribute};
        }
        return $collection;
    }

    protected function filterData( Collection $collection, $transformer ) {
        foreach ( request()->query() as $query => $value) {
            $attribute = $transformer::originalAttributes( $query );

            if ( isset( $attribute, $value ) ) {
                $collection = $collection->where( $attribute, $value );
            }
        }

        return $collection;
    }

    protected function transformData( $data, $transformer ) {
        $transformation = fractal( $data, new $transformer );

        return $transformation->toArray();
    }
}
