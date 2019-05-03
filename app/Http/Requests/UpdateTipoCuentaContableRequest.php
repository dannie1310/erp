<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/04/2019
 * Time: 05:26 PM
 */

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoCuentaContableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('editar_tipo_cuenta_contable');
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
            'id_naturaleza_poliza' => ['required', 'integer', 'exists:cadeco.Contabilidad.naturaleza_poliza,id_naturaleza_poliza']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }

}