<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $transformer)
    {
        $transformedInput = [];

        foreach ( $request->request->all() as $input => $value ) { // TODO: Se transforman los inputs de ingreso
            $transformedInput[ $transformer::originalAttributes( $input ) ] = $value;
        }

        $request->replace( $transformedInput );

        $response = $next($request);

        if ( isset( $response->exception ) && $response->exception instanceof ValidationException ) {
            $data = $response->getData();

            $transformedErrors = [];

            foreach ( $data->error as $field => $error) { // TODO: Se transforman los datos de errores
                $transformedField = $transformer::transformedAttributes( $field );
                $transformedErrors[ $transformedField ] = str_replace( $field, $transformedField, $error );
            }

            $data->error = $transformedErrors;

            $response->setData( $data );
        }

        return $response;
    }
}
