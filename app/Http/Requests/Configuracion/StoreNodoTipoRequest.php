<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 27/12/2019
 * Time: 04:37 PM
 */

namespace App\Http\Requests\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class StoreNodoTipoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_asignacion_nodo_tipo');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_concepto' => ['required', 'integer', 'exists:cadeco.dbo.conceptos,id_concepto'],
            'id_tipo_nodo' => ['required','integer', 'exists:cadeco.Configuracion.ctg_tipos_nodos,id'],
            'id_concepto_proyecto' => ['required', 'integer','exists:cadeco.dbo.conceptos,id_concepto'],
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}