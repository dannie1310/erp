<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2019
 * Time: 07:25 PM
 */

namespace App\Http\Requests\Subcontratos;


use Illuminate\Foundation\Http\FormRequest;

class AjustarSaldoFondoGarantiaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('ajustar_saldo_fondo_garantia');
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
            'id_fondo_garantia' => ['required', 'numeric'],
            'importe' => ['required', 'numeric'],
            'observaciones' => ['required', 'string'],
        ];
    }

}