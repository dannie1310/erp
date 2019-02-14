<?php

namespace App\Http\Requests;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;

class StoreCuentaEmpresaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_cuenta_empresa');
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
            'id_empresa' => ['required', 'integer', 'exists:cadeco.empresas,id_empresa'],
            'id_tipo_cuenta_empresa' => ['required', 'numeric', 'exists:cadeco.Contabilidad.tipos_cuentas_empresas,id']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}
