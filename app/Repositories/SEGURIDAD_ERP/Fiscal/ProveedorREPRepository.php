<?php

namespace App\Repositories\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use App\Models\SEGURIDAD_ERP\Fiscal\RepNotificacionCuerpoCorreo;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\CFDSATRepository;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\ProveedorSATRepository;

class ProveedorREPRepository extends Repository implements RepositoryInterface
{
    public function __construct(ProveedorREP $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function actualizarContactos($id, $destinatarios)
    {
        $proveedor = $this->show($id);
        $correos_destinatarios = [];
        foreach ($destinatarios as $destinatario) {
            $proveedor->getContacto($destinatario);
            $correos_destinatarios[] = $destinatario["correo"];
        }

        $correos_contactos = $proveedor->contactos->pluck("correo")->toArray();
        $correos_borrar = array_diff($correos_contactos, $correos_destinatarios);


        foreach ($correos_borrar as $correo_borrar) {
            $contactos_desactivar = $proveedor
                ->contactos()
                ->where("correo", "=", $correo_borrar)
                ->get();
            foreach($contactos_desactivar as $contacto_desactivar)
            {
                $contacto_desactivar->desactivar();
            }
        }

        return $proveedor;

    }

    public function registrarNotificacion($id, $data)
    {
        $proveedor = $this->show($id);
        $destinatarios = $data["destinatarios"];
        $cantidad_cfdi = $proveedor->cantidad_cfdi;
        $monto_mxn_cfdi = $proveedor->pendiente_rep;
        $id_cfdis_actuales = $proveedor->cfdiConAdeudoREP()->pluck("id_cfdi")->toArray();

        $ultima_notificacion = $proveedor->notificaciones()->orderBy("id","desc")->first();
        $id_cfdis_anteriores = $ultima_notificacion->notificacionCFDI()->pluck("id_cfdi")->toArray();

        $cantidad_nuevos = count(array_diff($id_cfdis_actuales, $id_cfdis_anteriores));
        $cantidad_atendidos = count(array_diff($id_cfdis_anteriores, $id_cfdis_actuales));

        $repositoryCFDI = new CFDSATRepository(new CFDSAT());
        $cantidad_cancelados = count($repositoryCFDI->whereIn(["id", $id_cfdis_anteriores])
            ->where([["cancelado","=",1]])
            ->all());

        $notificacion = $proveedor->notificaciones()->create([
            "cuerpo_correo" => $data["cuerpo_correo"],
            "cfdi_nuevos" => $cantidad_nuevos,
            "cfdi_atendidos" => $cantidad_atendidos,
            "cfdi_cancelados" => $cantidad_cancelados,
            "cantidad_cfdi" => $cantidad_cfdi,
            "monto_mxn_cfdi" => $monto_mxn_cfdi,
        ]);

        if ($notificacion) {
            foreach ($destinatarios as $destinatario) {
                $contacto = $proveedor->getContacto($destinatario);
                $notificacion->destinatarios()->create([
                        "id_contacto_proveedor" => $contacto->id,
                        "correo" => $contacto->correo,
                        "nombre" => $contacto->nombre,
                    ]
                );
            }

            foreach ($id_cfdis_actuales as $id_cfdi) {
                $notificacion->notificacionCFDI()->create([
                        "id_cfdi" => $id_cfdi,
                    ]
                );
            }
        }

        return $notificacion;
    }
    public function getCuerpoCorreo($id)
    {
        $repositoryProveedor = new ProveedorSATRepository(new ProveedorSAT());
        $proveedor = $repositoryProveedor->show($id);
        $repositoryCuerpo = new RepNotificacionCuerpoCorreoRepository(new RepNotificacionCuerpoCorreo());
        $cuerpo_correo = $repositoryCuerpo->model->where("estatus","=",1)->pluck("cuerpo_correo")->first();
        $cuerpo_correo = str_replace("[%razon_social%]",$proveedor->razon_social." (".$proveedor->rfc.")",$cuerpo_correo);

        return $cuerpo_correo;
    }

}
