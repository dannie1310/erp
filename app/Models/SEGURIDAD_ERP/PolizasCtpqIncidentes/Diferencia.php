<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use App\Models\CTPQ\Poliza;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Diferencia extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PolizasCtpqIncidentes.diferencias';
    public $timestamps = false;
    protected $fillable = [
        "id_poliza",
        "id_movimiento",
        "base_datos_revisada",
        "base_datos_referencia",
        "id_tipo",
        "fecha_hora_deteccion",
        "fecha_hora_resolucion",
        "observaciones",
        "tipo_busqueda",
        "id_busqueda"
    ];

    public static function buscarSO($data){
        $diferencia = null;
        if(key_exists("id_movimiento", $data)){
            $diferencia = Diferencia::activos()->where("id_poliza",$data["id_poliza"])
                ->where("id_movimiento",$data["id_movimiento"])
                ->where("base_datos_revisada",$data["base_datos_revisada"])
                ->where("base_datos_referencia",$data["base_datos_referencia"])
                ->where("id_tipo",$data["id_tipo"])
                ->where("tipo_busqueda",$data["tipo_busqueda"])
                ->first();
        } else {
            $diferencia = Diferencia::activos()->where("id_poliza",$data["id_poliza"])
                ->where("base_datos_revisada",$data["base_datos_revisada"])
                ->where("base_datos_referencia",$data["base_datos_referencia"])
                ->where("id_tipo",$data["id_tipo"])
                ->where("tipo_busqueda",$data["tipo_busqueda"])
                ->first();
        }
        return $diferencia;
    }

    public static function buscar($data)
    {
        $diferencia = null;
        if(key_exists("id_movimiento", $data)){
            $diferencia = Diferencia::activos()->where("id_poliza",$data["id_poliza"])
                ->where("id_movimiento",$data["id_movimiento"])
                ->where("base_datos_revisada",$data["base_datos_revisada"])
                ->where("base_datos_referencia",$data["base_datos_referencia"])
                ->where("id_tipo",$data["id_tipo"])
                ->where("tipo_busqueda",$data["tipo_busqueda"])
                ->where("observaciones",$data["observaciones"])
                ->first();
        } else {
            $diferencia = Diferencia::activos()->where("id_poliza",$data["id_poliza"])
                ->where("base_datos_revisada",$data["base_datos_revisada"])
                ->where("base_datos_referencia",$data["base_datos_referencia"])
                ->where("id_tipo",$data["id_tipo"])
                ->where("tipo_busqueda",$data["tipo_busqueda"])
                ->where("observaciones",$data["observaciones"])
                ->first();
        }
        return $diferencia;
    }
    public static function registrar($data){
        $diferencia = Diferencia::buscar($data);
        if(!$diferencia)
        {
            Diferencia::create($data);
        }
    }

    public function getFechaHoraDeteccionFormatAttribute()
    {
        $date = date_create($this->fecha_hora_deteccion);
        return date_format($date, "d/m/Y H:i:s");
    }

    public function getFechaHoraResolucionFormatAttribute()
    {
        if ($this->fecha_hora_resolucion) {
            $date = date_create($this->fecha_hora_resolucion);
            return date_format($date, "d/m/Y H:i:s");
        } else {
            return null;
        }

    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipo::class, "id_tipo", "id");
    }

    public function correccion(){
        return $this->hasOne(DiferenciaCorregida::class,"id_diferencia", "id");
    }

    public function scopeActivos($query)
    {
        return $query->where("activo",1);
    }


    public function poliza()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos_revisada);
        return $this->belongsTo(Poliza::class, "id_poliza", "Id");
    }

    public static function aplicarCorreccion($datos_correccion)
    {
        if(key_exists("id_movimiento", $datos_correccion)){
            $diferencia=Diferencia::activos()->where("id_poliza",$datos_correccion["id_poliza"])
                ->where("id_movimiento",$datos_correccion["id_movimiento"])
                ->where("base_datos_revisada",$datos_correccion["base_datos_revisada"])
                ->where("base_datos_referencia",$datos_correccion["base_datos_referencia"])
                ->where("id_tipo",$datos_correccion["id_tipo"])
                ->where("tipo_busqueda",$datos_correccion["tipo_busqueda"])
                ->first();
        } else {
            $diferencia=Diferencia::activos()->where("id_poliza",$datos_correccion["id_poliza"])
                ->where("base_datos_revisada",$datos_correccion["base_datos_revisada"])
                ->where("base_datos_referencia",$datos_correccion["base_datos_referencia"])
                ->where("id_tipo",$datos_correccion["id_tipo"])
                ->where("tipo_busqueda",$datos_correccion["tipo_busqueda"])
                ->first();

        }
        if($diferencia){
            $diferencia->corregir();
        }
    }

    public function corregir($id_busqueda = 1)
    {
        $this->activo = 0;
        $this->fecha_hora_resolucion = date('Y-m-d H:i:s');
        $this->correccion()->create(["id_busqueda"=>$id_busqueda]);
        $this->save();
    }
}