<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/02/2019
 * Time: 04:37 PM
 */

namespace App\Http\Requests;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;

class StoreCuentaBancoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_cuenta_contable_bancaria');
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

        return [
            'cuenta' => ['required', "regex:'{$regex}'"],
            'id_cuenta' => ['required', 'integer', 'exists:cadeco.dbo.cuentas,id_cuenta'],
            'id_tipo_cuenta_contable' => ['required', 'integer', 'exists:cadeco.Contabilidad.int_tipos_cuentas_contables,id_tipo_cuenta_contable']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}