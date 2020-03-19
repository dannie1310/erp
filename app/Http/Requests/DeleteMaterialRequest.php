<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMaterialRequest extends FormRequest
{
    /**
     * Determinar si esta autorizado para realizar esta operación
     * 
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can([
            'eliminar_insumo_servicio',
            'eliminar_insumo_material',
            'eliminar_insumo_herramienta_equipo',
            'eliminar_insumo_maquinaria',
            'eliminar_insumo_mano_obra']);
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
        abort(403, 'Permisos insuficientes para esta acción');
    }
}