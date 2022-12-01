<?php


namespace App\Models\CADECO\Inventarios;


use App\CSV\InventarioFisicoLayout;
use App\CSV\InventarioFisicoLayoutResumen;
use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\IGH\Usuario;
use App\PDF\Almacenes\InventarioMarbete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InventarioFisico extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.inventario_fisico';
    protected $primaryKey = 'id';

    protected $fillable = [
        'estado',
        'id_tipo'
    ];

    protected $searchable = [
        'fecha_hora_inicio'
    ];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function getNumeroFolioFormatAttribute()
    {
        return $this->tipoInventario->nombre_corto." ".str_pad($this->folio, 3,"0",STR_PAD_LEFT);
    }

    public static function calcularFolio()
    {
        $est = InventarioFisico::orderBy('folio', 'DESC')->first();
        return $est ? $est->folio + 1 : 1;
    }

    public function descargaLayout()
    {
        if (config('filesystems.disks.inventario_fisico_descarga.root') == storage_path())
        {
            dd('No existe el directorio destino: STORAGE_RESUMEN_LAYOUT_INVENTARIO_FISICO. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }
        Storage::disk('inventario_fisico_descarga')->delete(Storage::disk('inventario_fisico_descarga')->allFiles());
        $nombre_archivo = 'LayoutConteo_' . date('dmYY_His') . '.csv';
        (new InventarioFisicoLayout($this))->store($nombre_archivo, 'inventario_fisico_descarga');
        return Storage::disk('inventario_fisico_descarga')->download($nombre_archivo);
    }

    public function generar_resumen_conteos()
    {
        if (config('filesystems.disks.inventario_fisico_descarga.root') == storage_path())
        {
            dd('No existe el directorio destino: STORAGE_RESUMEN_LAYOUT_INVENTARIO_FISICO. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }
        Storage::disk('inventario_fisico_descarga')->delete(Storage::disk('inventario_fisico_descarga')->allFiles());
        $nombre_archivo = 'ResumenConteos_' . date('dmYY_His') . '.csv';
        (new InventarioFisicoLayoutResumen($this))->store($nombre_archivo, 'inventario_fisico_descarga');
        return Storage::disk('inventario_fisico_descarga')->download($nombre_archivo);
    }

    public function marbetes()
    {
        return $this->hasMany(Marbete::class, 'id_inventario_fisico','id');
    }

    public function obra()
    {
        return $this->belongsTo(Obra::class, 'id_obra', 'id_obra');
    }

    public function pdf_marbetes()
    {
        $marbetes = new InventarioMarbete($this);
        return $marbetes->create();
    }

    public function validar()
    {
        if(InventarioFisico::where('estado', '=',0)->first() != null){
            abort(400,'Existe un inventario físico no finalizado');
        }
        return true;
    }

    public function tipoInventario()
    {
        return $this->belongsTo(CtgTipoInventario::class, 'id_tipo', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_inicia', 'idusuario');
    }

    public function getFechaHoraInicioFormatAttribute()
    {
        $date = date_create($this->fecha_hora_inicio);
        return date_format($date,"d-m-Y h:i");

    }

    public function getCantidadMarbetesAttribute()
    {
        return count($this->marbetes);
    }

    public function getEstadoFormatAttribute()
    {
        if($this->estado == 0){
            return 'Abierto';
        }else{
            return 'Cerrado';
        }
    }
}
