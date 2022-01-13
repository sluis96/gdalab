<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use Validator;
use DB;
use Log;

class AuthController extends Controller
{

    /**
     * Registrar un nuevo usuario.
     *
     */
    public function register(Request $request)
    {
        try {
            DB::beginTransaction();

            $messages = [
                'name.required' => 'El nombre es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.',
                'email.required' => 'El correo es obligatorio.',
                'email.unique' => 'El correo ya se encuentra registrado.',
                'email.email' => 'Ingrese un correo valido.',
            ];

            $validator = Validator::make($request->all(), [
                'name' => ['required'],
                'email' => ['required', 'unique:users,email,NULL,id', 'email'],
                'password' => ['required'],
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Los datos enviados no son correctos',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $data = array(
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password), // Encriptar contraseña
            );

            $user = User::create($data);

            if (!$user->id) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'El usuario no se ha podido registrar',
                ], 500);
            }

            Log::channel('activity')->info('AuthController@register', ['user_id' => $user->id, 'status' => 'ok']);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'El usuario se ha registrado correctamente',
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
            ], 500);
        }
    }

    /**
     * Obtener un JWT a través de las credenciales dadas.
     *
     */
    public function login(Request $request)
    {

        $messages = [
            'password.required' => 'La contraseña es obligatoria.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingrese un correo valido.',
        ];

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Los datos enviados no son correctos',
                'errors' => $validator->errors(),
            ], 400);
        }

        $credentials = request(['email', 'password']);

        try {
            if (! $token = auth()->attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Credenciales no validas',
                ], 401);
            }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'No se pudo generar token de conexion',
            ], 500);
        }

        Log::channel('activity')->info('AuthController@login', ['user_id' => auth()->user()->id, 'status' => 'ok']);

        return response()->json([
            'status' => true,
            'message' => 'Conexion exitosa',
            'token' => $token,
        ]);
    }
}
