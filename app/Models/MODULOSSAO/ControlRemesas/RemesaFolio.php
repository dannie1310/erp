<?php


namespace App\Models\MODULOSSAO\ControlRemesas;


use App\Models\MODULOSSAO\Proyectos\Proyecto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RemesaFolio extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'ControlRemesas.RemesasFolios';
    public $timestamps = false;
    protected $fillable = [
        'CantidadExtraordinariasPermitidas',
        'MontoLimiteExtraordinarias'
    ];


    /**
     * Relaciones
     */
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'IDProyecto', 'IDProyecto');
    }

    /**
     * Scope
     */
    public function scopeOrdenarPorAnioSemana($query)
    {
        return $query->orderBy('Anio','desc')->orderBy('NumeroSemana','desc');
    }

    /**
     * Atributos
     */
    public function getNombreProyectoAttribute()
    {
        try{
            return $this->proyecto->Nombre;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getMontoLimiteFormatAttribute()
    {
        return '$' . number_format($this->MontoLimiteExtraordinarias,2);
    }

    /**
     * Métodos
     */
    public function editar(array $data)
    {
        $this->validarEdicion($data);
        try {
            DB::connection('modulosao')->beginTransaction();
            $this->where('IDProyecto', $data['id_proyecto'])->where('Anio', $data['anio'])->where('NumeroSemana', $data['numero_semana'])->update([
               'CantidadExtraordinariasPermitidas' => $data['cantidad_limite'],
               'MontoLimiteExtraordinarias' => $data['monto_limite']
            ]);
            DB::connection('modulosao')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('modulosao')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function validarEdicion($data)
    {
        /*if((Int) $this->Anio != (Int) date('Y') || (Int) $this->NumeroSemana != (Int) date('W')+1)
        {
            abort(400, "No se puede editar limite de remesa extraordinaria a semanas anteriores a la actual.");
        }*/
        $this->validarRemesas($data);
    }

    private function validarRemesas($data)
    {
        $remesas = Remesa::withoutGlobalScopes()->extraordinaria()->where('IDProyecto', $this->IDProyecto)->where('Anio', $this->Anio)->where('NumeroSemana', $this->NumeroSemana)->count();
        if($remesas > (Int) $data["cantidad_limite"])
        {
            abort(400, "No se puede actualizar la cantidad límite de remesas extraordinarias a ".$data["cantidad_limite"]." debido a que ya existen ".$remesas." remesas extraordinarias registradas.");
        }

        $remesas = Remesa::withoutGlobalScopes()->solicitada()->extraordinaria()->where('IDProyecto', $this->IDProyecto)->where('Anio', $this->Anio)->where('NumeroSemana', $this->NumeroSemana)->get();
        $sumatoria_solicitado = 0;
        foreach ($remesas as $remesa)
        {
            $sumatoria_solicitado += $remesa->monto_total_solicitado;
        }
        if($sumatoria_solicitado > ($data["monto_limite"]+0.99))
        {
            abort(400, "No se puede actualizar el monto límite de remesas extraordinarias a $".number_format($data["monto_limite"],2)." debido a que existen ".count($remesas)." remesas extraordinarias solicitadas que suman $".number_format($sumatoria_solicitado,2).".");
        }
    }
}
