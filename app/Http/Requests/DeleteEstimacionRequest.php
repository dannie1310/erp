<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 04/11/2019
 * Time: 02:00 PM
 */

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class DeleteEstimacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('eliminar_estimacion_subcontrato');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data' => ['required']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}