<?php


namespace App\Http\Requests\Compras;

use Illuminate\Foundation\Http\FormRequest;

class EliminarAsignacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('eliminar_asignacion_proveedor');
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