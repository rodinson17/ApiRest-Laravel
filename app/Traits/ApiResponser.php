<?php

namespace App\Traits;

//use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection; // TODO: Se recomienda usar esta importación
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

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
        $collection = $this->paginate( $collection );
        $collection = $this->transformData( $collection, $transformer );
        $collection = $this->cacheResponse( $collection );

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

    protected function transformData( $data, $transformer ) {
        $transformation = fractal( $data, new $transformer );

        return $transformation->toArray();
    }

    protected function sortData( Collection $collection, $transformer ) { // TODO: filtros
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

    protected function paginate( Collection $collection ) { // TODO: pagination
        $rules =  [ 'per_page' => 'integer|min:2|max:50' ];

        Validator::validate( request()->all(), $rules );

        $page = LengthAwarePaginator::resolveCurrentPage();

        $perPage = 15;

        if ( request()->has( 'per_page' ) ) $perPage = (int) request()->per_page;

        $results = $collection->slice( ($page - 1) * $perPage, $perPage )->values();

        $paginated = new LengthAwarePaginator( $results, $collection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $paginated->appends( request()->all() );

        return $paginated;
    }

    protected function cacheResponse( $data ) { // TODO: cache
        $url = request()->url();
        $queryParams = request()->query();

        ksort( $queryParams );

        $queryString = http_build_query( $queryParams );
        $fullUrl = "{$url}?{$queryString}";

        return Cache::remember( $fullUrl, 15/60, function() use( $data ) {
            return $data;
        });
    }
}
