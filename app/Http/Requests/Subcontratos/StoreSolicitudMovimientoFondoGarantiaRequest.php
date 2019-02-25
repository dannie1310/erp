<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 06/02/2019
 * Time: 06:23 PM
 */

namespace App\Http\Requests\Subcontratos;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudMovimientoFondoGarantiaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('consultar_cuenta_almacen');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'fecha' => ['required', 'date_format:"Y-m-d"'],
            'id_subcontrato' => ['required', 'numeric'],
            'importe' => ['required', 'numeric'],
            'observaciones' => ['required', 'string'],
            'referencia' => ['required', 'string'],
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }

}