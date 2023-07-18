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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ciudad' => ['filled', 'max:255', 'string'],
            'cliente' => ['max:255', 'string', 'filled'],
            'configuracion' => ['array', 'required'],

                'configuracion.esquema_permisos' => ['integer', 'required'],
                'configuracion.id_administrador' => ['integer', 'exists:igh.usuario,idusuario', 'filled'],
                'configuracion.id_responsable' => ['integer', 'exists:igh.usuario,idusuario', 'filled'],
                'configuracion.id_tipo_proyecto' => ['integer', 'exists:seguridad.ctg_tipos_proyecto,id', 'required'],
                'configuracion.logotipo_original' => ['image', 'mimes:png', 'filled'],

            'constructora' => ['max:255', 'string', 'filled'],
            'descripcion' => ['filled', 'max:255', 'string'],
            'direccion' => ['max:255', 'string'],
            'estado' => ['filled', 'max:255', 'string'],
            'facturar' => ['max:255', 'string', 'filled'],
            'fecha_final' => ['date_format:"Y-m-d"', 'required'],
            'fecha_inicial' => ['date_format:"Y-m-d"', 'required'],
            'id_moneda' => ['integer', 'exists:cadeco.monedas,id_moneda', 'required'],
            'iva' => ['numeric', 'required'],
            'nombre' => ['max:16', 'string', 'required'],
            'responsable' => ['max:64', 'string', 'filled'],
            'rfc' => ['max:16', 'string', 'filled'],
            'tipo_obra' => ['integer', 'required'],
            'valor_contrato' => ['filled', 'numeric'],
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}
