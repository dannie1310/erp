<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SetContextRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool        throw new ($validator->errors(), null, '400');

     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        Validator::extend('obra_exists', function ($attribute, $value, $parameters, $validator) {
            try {
                config()->set('database.connections.cadeco.database', $validator->getData()['db']);
                $query = DB::connection('cadeco')->table('obras')->select('*')->where($attribute, '=',  $value)->value($attribute);
                return $query == $value;
            } catch (\Exception $e) {
                return false;
            }
        });

        return [
            'db' => ['required', 'string', 'exists:seguridad.proyectos,base_datos'],
            'id_obra' => ['required', 'numeric', 'obra_exists']
        ];
    }
}