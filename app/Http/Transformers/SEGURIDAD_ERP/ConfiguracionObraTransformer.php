<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/19/19
 * Time: 6:44 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use League\Fractal\TransformerAbstract;

class ConfiguracionObraTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tipo'
    ];

    public function transform(ConfiguracionObra $model)
    {
        return [
            'id' => $model->getKey(),
            'logotipo_original' => request('logo') ? $this->getImagen($model->logotipo_original) : null,
            'id_obra' => $model->id_obra,
            'esquema_permisos' => $model->esquema_permisos,
            'id_tipo_proyecto' => $model->id_tipo_proyecto,
            'id_responsable' => $model->id_responsable,
            'id_administrador' => $model->id_administrador,
            'administrador' => $model->administrador_nombre,
            'responsable' => $model->responsable_nombre,
            'nombre' => $model->nombre,
            'base_datos' => $model->proyecto->base_datos,
            'id_proyecto' => $model->id_proyecto,
            'id_obra' => $model->id_obra,
            'consulta' => $model->consulta,
            'tipo_obra' => $model->tipo_obra,
        ];
    }

    public function includeTipo(ConfiguracionObra $model)
    {
        if ($tipo = $model->tipo) {
            return $this->item($tipo, new TipoProyectoTransformer);
        }
        return null;
    }

    private function getImagen($imagen = '')
    {
        $bin = '';
        $data = hex2bin($imagen);
        $file = public_path('img/logo_temp.png');
        if (file_put_contents($file, $data)){
            $bin = base64_encode($data);;
            unlink($file);
        }
        return $bin;
    }
}
