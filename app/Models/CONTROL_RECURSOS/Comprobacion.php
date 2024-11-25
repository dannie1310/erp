<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comprobacion extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'comprobaciones';
    protected $primaryKey = 'IdComprobacion';

    public $timestamps = false;
    protected $fillable = [
        'IdDepartamento',
        'Fecha',
        'Del',
        'Al',
        'IdUbicacionVisitada',
        'Ubicacion',
        'EmpresaFacturar',
        'Empleado',
        'Motivo',
        'Serie',
        'Folio',
        'DoctoOrigen',
        'ImpSol',
        'SaldoComp',
        'IdMoneda',
        'Creo',
        'Solicita',
        'JefeArea',
        'DirectorArea',
        'AutTramiteFinanzas',
        'Version',
        'Alias_Depto' ,
        'Cerro',
        'Fecha_RcbCCH',
        'Recibio_CCH',
        'Fecha_RcbCTb',
        'registro_portal'
    ];

    /**
     * Relaciones
     */
    public function solicitud()
    {
        return $this->belongsTo(SolChequeComprobacion::class, 'DoctoOrigen','IdSolCheques');
    }


    /**
     * Scopes
     */

    /**
     * Atributos
     */

    /**
     * Métodos
     */
    public function registrar($data)
    {
        try {
            DB::connection('controlrec')->beginTransaction();
            $usuario = Usuario::where('idusuario',auth()->id())->first();
            $creo = $usuario->nombre_completo. "|" . date("d-m-Y") . "|" . date("H:i:s", time());
            $firmas = $this->getFirmas($data['solicitud']['id_proveedor']);
            $comprobacion = $this->create([
                'IdDepartamento' => $data['solicitud']['iddepartamento'],
                'Fecha' => date("Y-m-d"),
                'Del' => $data['fecha_inicial'],
                'Al' => $data['fecha_final'],
                'IdUbicacionVisitada' => $data['id_proyecto'],
                'Ubicacion' => $data['solicitud']['ubicacion'],
                'EmpresaFacturar' => $data['solicitud']['idempresa'],
                'Empleado' => $data['solicitud']['id_proveedor'],
                'Motivo' => $data['solicitud']['concepto'],
                'DoctoOrigen' => $data['solicitud']['id'],
                'ImpSol' => $data['solicitud']['importe'],
                'SaldoComp' => $data['solicitud']['importe'],
                'IdMoneda' => $data['solicitud']['id_moneda'],
                'Solicita' => $data['solicitud']['empleado_descripcion'],
                'JefeArea' => $firmas ? $firmas->Autoriza : '',
                'DirectorArea' => $firmas ? $firmas->VoBo : '',
                'AutTramiteFinanzas' => $firmas ? $firmas->Aplicacion : '',
                'Version' => 2,
                'Alias_Depto' => $data['solicitud']['serie'],
                'Cerro' => 0,
                'Recibio_CCH' => 0,
                'registro_portal' => 1
            ]);
            $this->setFirmas();

            SolicitudComprobada::create([
                'IdSolcheque' => $data['solicitud']['id'],
                'IdComprobacion' => $comprobacion->getKey(),
                'ImporteSolicitud' => $comprobacion->solicitud->Importe,
                'ImporteComprobadoAnterior' => $comprobacion->solicitud->SaldoComprobado,
                'ImportePorComprobar' => $comprobacion->solicitud->Importe - $comprobacion->solicitud->SaldoComprobado
            ]);
            DB::connection('controlrec')->commit();
            return $comprobacion;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function getNumeroFolio()
    {
        $folio = self::where('serie', 'REL')->orderBy('Folio', 'desc')->pluck('Folio')->first();
        return $folio+1;
    }

    public function getFirmas($id_solicitante)
    {
        $firma = FirmaComprobacionEspecial::where('IdSolicitante', $id_solicitante)->first();
        if($firma == null)
        {
            $firma= FirmaComprobacion::where('IdUsuario', auth()->id())->first();
        }
        return $firma;
    }

    public function setFirmas(){

    }
}
