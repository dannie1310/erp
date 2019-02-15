<?php

namespace App\Http\Requests;

use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaGeneral;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class StoreCuentaGeneralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_cuenta_general');
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

        Validator::extend('sin_cuenta', function ($attribute, $value, $parameters, $validator) {
            return ! CuentaGeneral::query()->where('id_int_tipo_cuenta_contable', '=', $value)->first();
        });

        return [
            'id_int_tipo_cuenta_contable' => ['required', 'integer', 'exists:cadeco.Contabilidad.int_tipos_cuentas_contables,id_tipo_cuenta_contable,id_obra,' . Context::getIdObra(), 'sin_cuenta'],
            'cuenta_contable' => ['required', "regex:'{$regex}'"]
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}
