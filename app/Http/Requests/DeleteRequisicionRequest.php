<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 27/11/2019
 * Time: 09:52 AM
 */

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class DeleteRequisicionRequest extends FormRequest
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
        return [];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}