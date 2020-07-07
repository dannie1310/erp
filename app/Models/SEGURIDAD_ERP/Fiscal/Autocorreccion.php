<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Autocorreccion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.autocorrecciones';
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
        return $this->hasMany(CFDAutocorreccion::class, 'id_autocorreccion', 'id');
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y H:i:s");
    }

    public function registrar($data)
    {
        $this->validarPartidas($data['cfds']);
        try {
            DB::connection('seguridad')->beginTransaction();
                $autocorreccion = $this->create([
                    'id_proveedor_sat' => $data['efo']['proveedor']['id']
                ]);

                foreach ( $data['cfds'] as $partida)
                {
                    if($partida['selected']) {
                        CFDAutocorreccion::create([
                            'id_autocorreccion' => $autocorreccion->id,
                            'id_cfd_sat' => $partida['id'],
                            'uuid' => $partida['uuid']
                        ]);
                    }
                }
            DB::connection('seguridad')->commit();
            return $autocorreccion;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function validarPartidas($partidas)
    {
        foreach ($partidas as $partida)
        {
            if($partida['selected'])
            {
               if($this->partidas->where('uuid', '=', $partida['uuid'])->count() != 0)
               {
                   abort(400, "El CFD (".$partida['uuid'].") se encuentra en una autocorrección.");
               }
            }
        }
    }
}
