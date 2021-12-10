<?php

namespace App\Observers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinada;
use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaLog;

class EmpresaBoletinadaObserver
{
    /**
     * @param EmpresaBoletinada $empresa
     */
    public function creating(EmpresaBoletinada $empresa)
    {
        if(!$empresa->usuario_registro >0){
            $empresa->usuario_registro = auth()->id();
        }
    }

    /**
     * @param EmpresaBoletinada $empresa
     */
    public function deleting(EmpresaBoletinada $empresa)
    {

        EmpresaBoletinadaLog::create(
            [
                'id_empresa_boletinada' => $empresa->id,
                'id_tipo_boletinadas' => $empresa->id_tipo_boletinadas,
                'rfc' => $empresa->rfc,
                'razon_social' => $empresa->razon_social,
                'observaciones' => $empresa->observaciones,
                'usuario_registro' => $empresa->usuario_registro,
                'fecha_hora_registro' => $empresa->fecha_hora_registro,
                'usuario_elimino' => auth()->id(),
                'fecha_hora_eliminacion' => date('Y-m-d H:i:s'),
                'motivo_eliminacion' => '',
            ]
        );
    }
}
