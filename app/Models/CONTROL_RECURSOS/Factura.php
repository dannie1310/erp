<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;

class Factura extends Documento
{

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {

        });
    }

    /**
     * Relaciones
     */
    public function cfd()
    {
        return $this->belongsTo(FacturaRepositorio::class, 'IdDocto','id_documento_cr');
    }

    /**
     * Atributos
     */
    public function getUuidAttribute()
    {
        try {
            return $this->cfd->uuid;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getVencimientoEditarAttribute()
    {
        $date = date_create($this->Vencimiento);
        return date_format($date,"m/d/Y");
    }

    public function getColorEstadoAttribute()
    {
        switch ($this->Estatus)
        {
            case 1:
                return '#3386FF';
                break;
            case 0:
                return '#FFEC33';
                break;
            case 2:
                return '#00a65a';
                break;
            default:
                return '#d1cfd1';
                break;
        }
    }

    /**
     * Métodos
     */
    public function registrar($data)
    {
        /** EL front envía la fecha con timezone Z (Zero) (+6 horas), por ello se actualiza el time zone a America/Mexico_City
         * */
        $vencimiento = New DateTime($data["vencimiento"]);
        $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));

        $fecha = New DateTime($data["fecha"]);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));

        $usuario = Usuario::where('idusuario',auth()->id())->first();

        return $this->create([
            'FolioDocto' => $data['folio'],
            'IdTipoDocto' => $data['idtipodocto'],
            'TipoDocto' => 0,
            "IdEmpresa" => $data["id_empresa"],
            "IdProveedor" => $data["id_proveedor"],
            "Fecha" => $fecha->format('Y-m-d'),
            "Vencimiento" => $vencimiento->format('Y-m-d'),
            'Concepto' => $data["concepto"],
            "Importe" => $data["subtotal"],
            "IVA" => $data["impuesto"],
            "Total" => $data["total"],
            "Retenciones" => $data["retencion"],
            "TasaIva" => (int) $data['tasa_iva'],
            "OtrosImpuestos" => $data["otros"],
            "IdMoneda" => $data["id_moneda"],
            'Alias_Depto' => $usuario->departamento ? $usuario->departamento->departamento_abreviatura : '',
            'Departamento' => $usuario->departamento ? $usuario->departamento->departamento : '',
            'IdSerie' => $data['idserie'],
            'TC' => $data['tipo_cambio'],
            'tipo_cambio_excepcion'=> array_key_exists('tipo_cambio_excepcion', $data) ? $data['tipo_cambio_excepcion'] : 0,
            'uuid' => $data['uuid'],
            'Creo' => auth()->id(),
            'Estatus' => $data['idtipodocto'] == 1 ? 1 : 5,
            'Ubicacion' => $usuario->ubicacion ? $usuario->ubicacion->ubicacion : '',
            "registro_portal" => 1
        ]);
    }

    public function editar(array $data)
    {
        $this->validaEstado();
        try {
            DB::connection('controlrec')->beginTransaction();
            $vencimiento = New DateTime($data["vencimiento"]);
            $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));
            $this->update([
                'IdTipoDocto' => $data['id_tipo'],
                'IdProveedor' => $data["id_proveedor"],
                "Vencimiento" => $vencimiento->format('Y-m-d'),
                'Concepto' => $data["concepto"],
                'IdSerie' => $data['id_serie'],
                'Estatus' => $data['id_tipo'] == 1 ? 1 : 5
            ]);
            DB::connection('controlrec')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function desvinculaFacturaRepositorio()
    {
        if ($this->cfd) {
            $this->cfd->id_documento_cr = null;
            $this->cfd->usuario_asocio = null;
            $this->cfd->fecha_hora_asociacion = null;
            $this->cfd->save();
        }
    }
}
