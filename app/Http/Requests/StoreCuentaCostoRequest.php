<?php

namespace App\Http\Requests;

use App\Facades\Context;
use App\Models\CADECO\Costo;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class StoreCuentaCostoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_cuenta_costo');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        try {
            $regex = Obra::query()->find(Context::getIdObra())->datosContables->FormatoCuentaRegExp;
        } catch (\Exception $e) {
            $regex = "";
        }
//
//        Validator::extend('sin_cuenta', function ($attribute, $value, $parameters, $validator) {
//           return Costo::sinCuenta()->find($value);
//        });

        return [
            'cuenta' => ['required', "regex:'{$regex}'"],
            'id_costo' => ['required', 'integer', 'exists:cadeco.costos,id_costo,id_obra,' . Context::getIdObra()],
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}
