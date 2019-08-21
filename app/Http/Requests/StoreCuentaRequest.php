<?php


namespace App\Http\Requests;


use App\Facades\Context;
use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class StoreCuentaRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('registrar_cuenta_corriente');
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        Validator::extend('saldo_minimo', function ($attribute, $value, $parameters, $validator) {
            return $value >= 0;
        });

        return [
            'id_empresa' => ['required', 'integer', 'exists:cadeco.empresas'],
            'id_moneda' => ['required', 'integer', 'exists:cadeco.monedas'],
            'numero' => ['required', 'numeric', 'digits_between:9,18'],
            'saldo_inicial' => ['required', 'saldo_minimo'],
            'fecha_inicial' => ['required'],
            'chequera' => ['required', 'integer'],
            'abreviatura' => ['required', 'string'],
            'id_tipo_cuentas_obra' => ['required', 'integer']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }

}
