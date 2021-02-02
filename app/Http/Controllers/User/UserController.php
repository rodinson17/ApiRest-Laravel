<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Mail\UserCreated;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController // TODO: Hereda de ApiController
{
    public function __construct()
    {
        parent::__construct();
        // TODO: Using middleware
        $this->middleware( 'transform.input:' . UserTransformer::class )->only([ 'store', 'update' ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listUsers = User::all();

        //return $listUsers;
        //return response()->json( [ 'data' => $listUsers ], 200 );
        return $this->showAll( $listUsers );  // TODO: implementacion del trait ApiResponser
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
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ];

        $this->validate( $request, $rules );

        $fields = $request->all();
        $fields['password'] = bcrypt( $request->password );
        $fields['verified'] = User::UNVERIFIED_USER;
        $fields['verification_token'] = User::generateVerificationToken();
        $fields['admin'] = User::REGULAR_USER;

        $user = User::create( $fields );

        //return response()->json([ 'data' => $user ], 201);
        return $this->showOne( $user, 201 );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //public function show($id)
    public function show( User $user ) // TODO: Inyección de modelos
    {
        //$user = User::findOrFail( $id );

        //return response()->json([ 'data' => $user ], 200);
        return $this->showOne( $user );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //public function update(Request $request, $id)
    public function update(Request $request, User $user)
    {
        //$user = User::findOrFail( $id );

        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMINISTATOR . ',' . User::REGULAR_USER
        ];

        $this->validate( $request, $rules );

        if ( $request->has('name') ) $user->name = $request->name;

        if ( $request->has('email') && ( $user->email != $request->email ) ) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationToken();
            $user->email = $request->email;
        }

        if ( $request->has('password') ) $user->password = bcrypt( $request->password );

        if ( $request->has('admin') ) {
            if ( !$user->isVerified() ) {
                //return response()->json([ 'error' => 'No es posible realizar este cambio', 'code' => 409 ], 409);
                return $this->errorResponse( 'No es posible realizar este cambio', 409 );
            }
            $user->admin = $request->admin;
        }

        if ( !$user->isDirty() ) {
            //return response()->json([ 'error' => 'Se debe especificar al menos un valor para actualizar', 'code' => 422 ], 422);
            return $this->errorResponse( 'Se debe especificar al menos un valor para actualizar', 422 );
        }

        $user->save();

        //return response()->json([ 'data' => $user ], 200);
        return $this->showOne( $user );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //public function destroy($id)
    public function destroy( User $user )
    {
        //$user = User::findOrFail( $id );
        $user->delete();

        //return response()->json([ 'data' => $user ], 200);
        return $this->showOne( $user );
    }

    public function verify( $token ) {
        $user = User::where( 'verification_token', $token )->firstOrFail();

        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;

        $user->save();

        return $this->showMessage( 'La cuenta ha sido verificada' );
    }

    public function resend( User $user ) { // TODO: Metodo para reenvio de correos
        if ( $user->isVerified() ) {
            return $this->errorResponse( 'Este usuario ya ha sido verificado.', 409 );
        }

        retry( 5, function() use ( $user ) { // TODO: retry: Intenta reenviar los correos en caso de fallo
            Mail::to( $user )->send( new UserCreated( $user ) );
        }, 100);

        return $this->showMessage( 'El correo de verificación se ha reenviado' );
    }
}
