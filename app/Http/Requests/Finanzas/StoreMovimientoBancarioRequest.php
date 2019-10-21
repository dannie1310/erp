<?php

namespace App\Http\Requests\Finanzas;

use App\Models\CADECO\Cuenta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class StoreMovimientoBancarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_movimiento_bancario');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        Validator::extend('para_traspaso', function ($attribute, $value, $parameters, $validator) {
            return Cuenta::query()->paraTraspaso()->find($value);
        });

        return [
            'cumplimiento' => ['required', 'date_format:"Y-m-d"'],
            'fecha' => ['required', 'date_format:"Y-m-d"'],
            'id_cuenta' => ['required', 'exists:cadeco.cuentas,id_cuenta', 'para_traspaso'],
            'id_tipo_movimiento' => ['required', 'exists:cadeco.Tesoreria.tipos_movimientos,id_tipo_movimiento'],
            'importe' => ['required', 'numeric'],
            'impuesto' => ['required_if:id_tipo_movimiento,4'],
            'observaciones' => ['required', 'string'],
            'referencia' => ['required', 'string'],
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}
