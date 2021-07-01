<?php


namespace App\Models\MODULOSSAO\ControlRemesas;


use App\Models\CADECO\Empresa;
use App\Models\CADECO\Transaccion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DocumentoDestinatario extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'ModulosSAO.ControlRemesas.DocumentosDestinatario';
    protected $primaryKey = 'IDDestinatario';
    public $timestamps = false;

    public function empresa(){
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database',  $this->documento->remesaSinScopeGlobal->proyecto->unificacionProyectoObra->baseDatosObraSinScopeGlobal->BaseDatos);
        return $this->belongsTo(Empresa::class, 'IDEmpresaCDC', 'id_empresa')->withoutGlobalScopes();
    }

    public function documento(){
        return $this->belongsTo(Documento::class, 'IDDestinatario', 'IDDestinatario')->withoutGlobalScopes();
    }

}
