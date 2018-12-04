<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'database' => ['required', 'string', 'exists:seguridad.proyectos,base_datos'],
            'id_obra' => ['required', 'numeric']
        ];
    }
}