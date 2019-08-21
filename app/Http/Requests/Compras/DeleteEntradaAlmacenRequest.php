<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 09:20 PM
 */

namespace App\Http\Requests\Compras;


use Illuminate\Foundation\Http\FormRequest;

class DeleteEntradaAlmacenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('eliminar_entrada_almacen');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'motivo' => ['required'],
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}