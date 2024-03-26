<?php


namespace App\Models\ACTIVO_FIJO;


use App\Models\IGH\Empresa;
use App\Models\IGH\Usuario;
use App\Models\IGH\Ubicacion;
use App\Models\IGH\Departamento;
use App\Models\ACTIVO_FIJO\GrupoActivo;
use Illuminate\Database\Eloquent\Model;
use App\Models\ACTIVO_FIJO\ResguardoElementoPdf;
use Illuminate\Support\Facades\DB;

class Resguardo extends Model
{
    protected $connection = 'sci';
    protected $table = 'resguardos';
    public $primaryKey = 'IdResguardo';
    protected $fillable = [
    ];

    /**
     * Relaciones Eloquent
     */

    public function departamento(){
        return $this->belongsTo(Departamento::class, 'IdDepartamento', 'iddepartamento');
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class,'IdEmpresa', 'idempresa');
    }

    public function firmasPorResguardo(){
        return $this->hasMany(ResguardoFirmasPorResguardo::class, 'idResguardo', 'IdResguardo');
    }

    public function grupoActivo(){
        return $this->belongsTo(GrupoActivo::class, 'GrupoEquipo', 'idGrupo');
    }

    public function partidas(){
        return $this->hasMany(ResguardoPartida::class, 'idResguardo', 'IdResguardo');
    }

    public function partidasEmpresa(){
        return $this->hasMany(ResguardoPartida::class, 'idResguardo', 'IdResguardo')->where('IdEstado', '!=', 22);
    }

    public function resguardoElemento(){
        return $this->belongsTo(ResguardoElementoPdf::class, 'GrupoEquipo', 'IdGrupoActivo');
    }

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'IdEmpleado', 'idusuario');
    }

    public function ubicacion(){
        return $this->belongsTo(Ubicacion::class, 'IdProyecto', 'idubicacion');
    }

    /**
     * Scopes
     */

    /**
     * Atributtes
     */

    public function getTipoGrupoActivoAttribute(){
        if($this->GrupoEquipo == 0){
            return 'Material de Oficina, Mobiliario y Equipo de Oficina';
        }
        return $this->grupoActivo->descripcion;
    }

    public function getFirmasValidasAttribute(){

            $entrega = $this->firmasPorResguardo()->where("IdFirma","=",2)->first();
            if($entrega != null && !$entrega)
            {
                $entrega = "";
                $entrega_grupo = ResguardoFirmaGrupo::where("IdFirma","=","2")
                ->where("IdGrupoActivo","=",$this->GrupoEquipo)
                ->first();
                $entrega = $entrega_grupo->Valor;
                DB::connection('sci')->insert("insert resguardos_firmas_x_resguardo (IdResguardo, IdFirma, Valor)
                values (".$this->IdResguardo .",2,'".$entrega."')");
            }

            $vobo = $this->firmasPorResguardo()->where("IdFirma","=",3)->first();
            if(!$vobo)
            {
                DB::connection('sci')->insert("insert resguardos_firmas_x_resguardo (IdResguardo, IdFirma)
                values (".$this->IdResguardo .",3)");
            }

            $sin_admon = $this->firmasPorResguardo()
                ->where(function ($q){
                    $q->whereNull("Valor")
                        ->orWhere("Valor","=","");
                })
                ->where("IdFirma","=",3)
                ->first();
            if($sin_admon)
            {
                if($this->ubicacion->administrador<>""){
                    DB::connection('sci')->update("update resguardos_firmas_x_resguardo
                    set Valor = '".$this->ubicacion->administrador."' where IdResguardo =".$this->IdResguardo ." and IdFirma=3 ");
                }
            }

        return $this->firmasPorResguardo()
        ->join('resguardos_firmas', 'resguardos_firmas.IdFirma', 'resguardos_firmas_x_resguardo.IdFirma')
        ->orderBy('resguardos_firmas.Orden', 'ASC')->get();
    }

    public function getNombreUsuarioInfoAttribute(){
        $usuario = DB::connection('sci')->select(DB::raw("select nombre, apaterno, amaterno from igh.usuario  where idusuario=".$this->IdEmpleado));
        return $usuario ? $usuario[0]->nombre." ".$usuario[0]->apaterno." ".$usuario[0]->amaterno : 'NO SE ENCONTRO EL USUARIO';
    }
}
