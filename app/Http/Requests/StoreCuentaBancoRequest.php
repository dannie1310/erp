<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/02/2019
 * Time: 04:37 PM
 */

namespace App\Http\Requests;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaBanco;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

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
        Validator::extend('disponible', function ($attribute, $value, $parameters, $validator) {

            $id_cuenta = $validator->getData()['id_cuenta'];

            //dd($id_cuenta);

            return ! CuentaBanco::query()
                ->where('id_cuenta', '=', $id_cuenta)
                ->where($attribute, '=', $value)
                ->first();
        });


        try {
            $regex = Obra::query()->find(Context::getIdObra())->datosContables->FormatoCuentaRegExp;
        } catch (\Exception $e) {
            $regex = "";
        }

        return [
            'cuenta' => ['required', "regex:'{$regex}'"],
            'id_cuenta' => ['required', 'integer', 'exists:cadeco.dbo.cuentas,id_cuenta'],
            'id_tipo_cuenta_contable' => ['required', 'integer', 'exists:cadeco.Contabilidad.int_tipos_cuentas_contables,id_tipo_cuenta_contable', 'disponible']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}