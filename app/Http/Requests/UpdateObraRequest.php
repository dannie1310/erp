<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/03/2019
 * Time: 09:21 PM
 */

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class UpdateObraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('administracion_configuracion_obra');
    }

    /** codigo_postal  int default 0
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'logotipo_original' => ['filled', 'image'],
            'nombre' => ['max:16', 'string'],
            'constructora' => ['max:255', 'string'],
            'cliente' => ['max:255', 'string'],
            'facturar' => ['max:255', 'string'],
            'responsable' => ['max:64', 'string'],
            'rfc' => ['max:16', 'string'],
            'id_moneda' => ['integer', 'exists:cadeco.monedas,id_moneda'],
            'iva' => ['numeric'],
            'fecha_inicial' => ['date_format:"Y-m-d"'],
            'fecha_final' => ['date_format:"Y-m-d"'],
            'tipo_obra' => ['integer'],
            'configuracion_esquema_permisos' => ['integer'],
            'configuracion_logotipo_original' => ['image', 'mimes:png'],
            'configuracion_id_tipo_proyecto' => ['integer', 'exists:seguridad.ctg_tipos_proyecto,id']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}