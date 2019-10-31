<?php

namespace App\Http\Requests\Finanzas;

use App\Models\CADECO\Cuenta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class UpdateMovimientoBancarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('editar_movimiento_bancario');
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
            'cumplimiento' => ['date_format:"Y-m-d"'],
            'fecha' => ['date_format:"Y-m-d"'],
            'id_cuenta' => ['exists:cadeco.cuentas,id_cuenta', 'para_traspaso'],
            'id_tipo_movimiento' => ['exists:cadeco.Tesoreria.tipos_movimientos,id_tipo_movimiento'],
            'importe' => ['numeric'],
            'impuesto' => ['numeric'],
            'observaciones' => ['filled', 'string'],
            'referencia' => ['filled', 'string'],
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}
