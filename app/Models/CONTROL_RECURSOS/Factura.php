<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use DateTime;
use DateTimeZone;

class Factura extends Documento
{

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereIn('IdTipoDocto', [1,6])->whereIn('Estatus',[1,5]);
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

    /**
     * Métodos
     */
    public function registrar($data)
    {
        /** EL front envía la fecha con timezone Z (Zero) (+6 horas), por ello se actualiza el time zone a America/Mexico_City
         * */
        $emision = New DateTime($data["emision"]);
        $emision->setTimezone(new DateTimeZone('America/Mexico_City'));

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
            'TC' => 1,
            'Creo' => auth()->id(),
            'Estatus' => $data['idtipodocto'] == 1 ? 1 : 5,
            'Ubicacion' => $usuario->ubicacion ? $usuario->ubicacion->ubicacion : ''
        ]);
    }
}
