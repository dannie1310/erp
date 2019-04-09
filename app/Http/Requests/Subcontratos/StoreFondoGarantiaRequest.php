<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 07/03/2019
 * Time: 09:29 AM
 */

namespace App\Http\Requests\Subcontratos;


use Illuminate\Foundation\Http\FormRequest;

class StoreFondoGarantiaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('generar_fondo_garantia');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_subcontrato' => ['required', 'numeric'],
            ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }

}