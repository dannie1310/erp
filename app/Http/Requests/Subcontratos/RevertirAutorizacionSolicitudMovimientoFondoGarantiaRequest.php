<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 18/02/2019
 * Time: 01:04 PM
 */

namespace App\Http\Requests\Subcontratos;


use Illuminate\Foundation\Http\FormRequest;

class RevertirAutorizacionSolicitudMovimientoFondoGarantiaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('revertir_autorizacion_solicitud_movimiento_fondo_garantia');
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required', 'numeric'],
            'observaciones' => ['required', 'string'],
        ];
    }
}