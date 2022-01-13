<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

use Validator;
use DB;
use Log;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Registra un nuevo cliente.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     */
    public function create(Request $request)
    {
        //
        try {
            DB::beginTransaction();

            $messages = [
                'dni.required' => 'El Documento de Indentidad es obligatorio.',
                'dni.unique' => 'Ya se encuentra registrado un cliente con el mismo Documento de Indentidad.',
                'id_reg.required' => 'La Region es obligatorio.',
                'id_reg.exists' => 'La Region seleccionada no existe.',
                'id_com.required' => 'La Comuna es obligatorio.',
                'id_com.exists' => 'La Comuna seleccionada no existe y/o no esta relacionada a la Region seleccionada.',
                'email.required' => 'El Correo es obligatorio.',
                'email.unique' => 'El correo ya se encuentra registrado.',
                'email.email' => 'Ingrese un correo valido.',
                'name.required' => 'El Nombre es obligatoria.',
                'name.string' => 'Ingrese un nombre valido.',
                'last_name.required' => 'El Apellido es obligatoria.',
                'last_name.string' => 'Ingrese un apellido valido.',
                'address.string' => 'Ingrese una direccion valida.',
            ];

            $validator = Validator::make($request->all(), [
                'dni' => [
                    'required',
                    Rule::unique('customers')->where(function ($query){
                        return $query->where('status', '<>', 'trash');
                    }),
                ],
                'id_reg' => [
                    'required',
                    Rule::exists('regions','id_reg')->where(function ($query){
                        $query->where('status', '<>', 'trash');
                    }),
                ],
                'id_com' => [
                    'required',
                    Rule::exists('communes','id_com')->where(function ($query) use ($request){
                        $query->where('id_reg', $request->id_reg);
                    }),
                ],
                'email' => [
                    'required', 
                    'email',
                    Rule::unique('customers')->where(function ($query){
                        return $query->where('status', '<>', 'trash');
                    }),
                    'max:120',
                ],
                'name' => ['required', 'string', 'max:45'],
                'last_name' => ['required', 'string', 'max:45'],
                'address' => ['nullable', 'string', 'max:255'],
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los datos enviados no son correctos',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Datos Obligatorios
            $data = array(
                'dni' => $request->dni,
                'id_reg' => $request->id_reg,
                'id_com' => $request->id_com,
                'email' => $request->email,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'date_reg' => date('Y-m-d H:i:s'),
            );

            $customer = Customer::create($data);

            if (!$customer->dni) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no se ha creado',
                ], 500);
            }

            // Datos Opcionales
            $customer->address = isset($request->address) ? $request->address : null;
            if (!$customer->save()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no se ha creado',
                ], 500);
            }

            Log::channel('activity')->info('CustomerController@create', ['user_id' => auth()->user()->id, 'status' => 'ok']);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'El cliente se ha creado correctamente',
                'customer' => $customer,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
            ], 500);
        }
    }

    /**
     * Retorna el contenido de un cliente en especifico.
     *
     * @param  \Illuminate\Http\Request  $request
     * search: Dni o Email del cliente
     */
    public function get(Request $request)
    {
        try {
            DB::beginTransaction();

            $messages = [
                'search.required' => 'Ingrese el Dni o Email.',
            ];

            $validator = Validator::make($request->all(), [
                'search' => [
                    'required',
                ],
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los datos enviados no son correctos',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $customer = Customer::with('commune.region')
                                ->where(function($query) use ($request){
                                    $query->where('dni', $request->search)
                                          ->orWhere('email', $request->search);
                                })
                                ->where('status', 'A')
                                ->first();

            if(is_null($customer)){
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado',
                ], 404);
            }

            $cliente = array(
                'name' => $customer->name,
                'last_name' => $customer->last_name,
                'address' => $customer->address,
                'commune_description' => $customer->commune->description,
                'region_description' => $customer->commune->region->description,
            );

            Log::channel('activity')->info('CustomerController@get', ['user_id' => auth()->user()->id, 'status' => 'ok']);

            DB::commit();
            return response()->json([
                'success' => true,
                'customer' => $cliente,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
            ], 500);
        }
    }

    /**
     * Elimina un cliente.
     *
     * @param  \Illuminate\Http\Request  $request
     * attribute: Dni o Email del cliente
     */
    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $messages = [
                'attribute.required' => 'Ingrese el Dni o Email.',
            ];

            $validator = Validator::make($request->all(), [
                'attribute' => [
                    'required',
                ],
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los datos enviados no son correctos',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $customer = Customer::where(function($query) use ($request){
                                    $query->where('dni', $request->attribute)
                                          ->orWhere('email', $request->attribute);
                                })
                                ->where('status', '<>', 'trash')
                                ->first();

            if(is_null($customer)){
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no existe',
                ], 404);
            }

            $customer->status = 'trash';
            if (!$customer->save()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no se ha eliminado',
                ], 500);
            }

            Log::channel('activity')->info('CustomerController@delete', ['user_id' => auth()->user()->id, 'status' => 'ok']);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'El cliente se ha eliminado correctamente',
                'customer' => $customer,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
            ], 500);
        }
    }
}
