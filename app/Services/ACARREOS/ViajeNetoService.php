<?php


namespace App\Services\ACARREOS;


use App\Facades\Context;
use App\Http\Transformers\ACARREOS\Catalogos\TiroTransformer;
use App\Models\ACARREOS\Camion;
use App\Models\ACARREOS\Checador;
use App\Models\ACARREOS\DeductivaMotivo;
use App\Models\ACARREOS\Material;
use App\Models\ACARREOS\Origen;
use App\Models\ACARREOS\Ruta;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Models\ACARREOS\SCA_CONFIGURACION\RolUsuario;
use App\Models\ACARREOS\Tag;
use App\Models\ACARREOS\Telefono;
use App\Models\ACARREOS\TipoImagen;
use App\Models\ACARREOS\Tiro;
use App\Models\ACARREOS\ViajeNeto;
use App\Models\IGH\Usuario;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class ViajeNetoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ViajeNetoService constructor.
     * @param ViajeNeto $model
     */
    public function __construct(ViajeNeto $model)
    {
        $this->repository = new Repository($model);
    }

    private function conexionAcarreos()
    {
        try{
            DB::purge('acarreos');
            \Config::set('database.connections.acarreos.database',Proyecto::pluck('base_datos')->first());
        }catch (\Exception $e){
            abort(500, "El proyecto no se encuentra activo en acarreos.");
            throw $e;
        }
    }
    public function getCatalogo($data)
    {
       // $this->conexionAcarreos();
        $tiros = Tiro::activo()->select(['idtiro', 'descripcion', 'IdEsquema'])->get()->toArray();
        $camiones = Camion::select(['idcamion', 'Placas','PlacasCaja','marcas.Descripcion as marca','Modelo', 'Ancho', 'largo', 'Alto', 'Economico', 'CubicacionParaPago', 'IdEmpresa', 'IdSindicato', 'camiones.Estatus'])
            ->activo()
            ->marcaDescripcion()
            ->get()->toArray();
        $materiales = Material::activo()->select(['idmaterial', 'descripcion'])->get()->toArray();
        $origenes = Origen::activo()->select(['idOrigen as idorigen', 'Descripcion as descripcion', 'Estatus as estado'])->get()->toArray();
        $rutas = Ruta::activo()->select(['clave', 'idruta', 'idorigen', 'idtiro', 'totalkm'])->get()->toArray();
        $tags = Tag::activo()->camionEconomico()->select(['uid', 'tags.idcamion', 'idproyecto_global as idproyecto', 'camiones.Economico',
            'camiones.Estatus'])->get()->toArray();
        $checadores = $this->arrayChecadores();
        $tipoImagenes = TipoImagen::activo()->select(['id', 'descripcion'])->get()->toArray();
        $motivoDeductiva = DeductivaMotivo::activo()->select(['id', 'motivo'])->get()->toArray();
        $telefonos = Telefono::activo()->impresoraActiva()->select('id', 'imei as IMEI', 'id_impresora')->where('imei', $data['IMEI'])->first();
        array_add($telefonos, 'MAC', $telefonos->impresora->mac);
        array_except($telefonos, 'impresora');
        $usuario = Usuario::where('idusuario', auth()->id())->first();
        $proyecto = Proyecto::query()->first();
       // dd($proyecto);

        dd( [
            'idusuario' => auth()->id(),
          'nombre' => $usuario->nombre_completo,
          'base_datos' => $proyecto->base_datos,
          'base_datos_cadeco' => $proyecto->base_SAO,
          'id_obra' => $proyecto->id_obra_cadeco,
          'camiones' => $camiones,
          'tiros' => $tiros,
          'origenes' => $origenes,
          'materiales' => $materiales,
          'rutas' => $rutas,
          'tags' => $tags,
          'checadores' => $checadores,
          'tipoImagenes' => $tipoImagenes,
          'motivoDeductiva' => $motivoDeductiva,
          'telefonos' => $telefonos->toArray()
        ]);


    }

    private function arrayChecadores()
    {
        $checadores = RolUsuario::checador()->get();
        $arrays = [];
        foreach ($checadores as $key => $checador) {
            $arrays[$key]['id'] = $checador->user_id;
            $arrays[$key]['descripcion'] = $checador->usuario->nombre_completo;
        }
        return $arrays;
    }
}
