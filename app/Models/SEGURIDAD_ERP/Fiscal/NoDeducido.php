<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NoDeducido extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.no_deducidos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_proveedor_sat',
        'fecha_hora_registro',
        'usuario_registro',
        'estado'
    ];

    public $timestamps = false;

    public function proveedor()
    {
        return $this->belongsTo(ProveedorSAT::class, 'id_proveedor_sat', 'id');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function partidas()
    {
        return $this->hasMany(CFDNoDeducido::class, 'id_no_deducido', 'id');
    }

    public function efo()
    {
        return $this->belongsTo(EFOS::class, 'id_proveedor_sat', 'id_proveedor_sat');
    }

    public function ctgEstado()
    {
        return $this->belongsTo(CtgEstadoCFD::class, 'estado', 'id');
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y H:i:s");
    }

    public function registrar($data)
    {
        $this->validarPartidas($data['cfd']);
        try {
            DB::connection('seguridad')->beginTransaction();
            $nocorreccion = $this->create([
                'id_proveedor_sat' => $data['efo']['proveedor']['id']
            ]);

            foreach ( $data['cfd'] as $partida)
            {
                if(array_key_exists('selected',$partida)== false || $partida['selected'])
                {
                    CFDNoDeducido::create([
                        'id_no_deducido' => $nocorreccion->id,
                        'id_cfd_sat' => $partida['id'],
                        'uuid' => $partida['uuid']
                    ]);
                }
            }
            DB::connection('seguridad')->commit();
            return $nocorreccion;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function validarPartidas($partidas)
    {
        foreach ($partidas as $partida)
        {
            if(array_key_exists('selected',$partida)== false || $partida['selected'] == true)
            {
                if($this->partidas->where('uuid', '=', $partida['uuid'])->count() != 0)
                {
                    abort(400, "El CFD (".$partida['uuid'].") se encuentra en una autocorrección.");
                }
            }
        }
    }

    public function getTotalPartidasAttribute()
    {
        $suma = 0;
        foreach ($this->partidas as $partida)
        {
             $suma += $partida->cfdSat->total;
        }
        return $suma;
    }

    public function getCantidadPartidasAttribute()
    {
        return number_format($this->partidas->count(),0);
    }
}
