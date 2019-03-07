<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 07/03/2019
 * Time: 12:57 PM
 */

namespace App\Http\Requests\Subcontratos;




use Illuminate\Foundation\Http\FormRequest;

class ShowSolicitudMovimientoFondoGarantiaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('consultar_solicitud_movimiento_fondo_garantia');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }


}