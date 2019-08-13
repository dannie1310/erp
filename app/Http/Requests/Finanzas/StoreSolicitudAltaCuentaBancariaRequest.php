<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/08/2019
 * Time: 07:54 PM
 */

namespace App\Http\Requests\Finanzas;


use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudAltaCuentaBancariaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('consular_solicitud_alta_cuenta_bancaria_empresa');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_tipo_empresa' => ['required', 'integer'],
            'id_empresa' => ['required', 'exists:cadeco.empresas,id_empresa'],
            'id_banco' => ['required', 'exists:cadeco.empresas,id_empresa'],
            'id_tipo' => ['required', 'integer'],
            'cuenta' => ['required', 'numeric'],
            'id_moneda' => ['required', 'exists:cadeco.monedas,id_moneda'],
            'sucursal' => ['required', 'integer', 'digits:3'],
            'id_plaza' => ['required', 'exists:seguridad.Finanzas.ctg_plazas,id'],
            'observaciones' => ['required', 'string'],
            'archivo' => ['required']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}