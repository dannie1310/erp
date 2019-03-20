<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/03/2019
 * Time: 01:24 PM
 */

namespace App\Http\Requests;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;

class StoreTipoCuentaContableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_tipo_cuenta_contable');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'descripcion' => ['required', "string"],
            'id_naturaleza' => ['required', 'integer', 'exists:cadeco.Contabilidad.naturaleza_poliza,id_naturaleza_poliza']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}