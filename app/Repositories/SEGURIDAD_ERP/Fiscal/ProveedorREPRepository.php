<?php

namespace App\Repositories\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

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

    public function registrarNotificacion($id, $destinatarios)
    {
        $proveedor = $this->show($id);

        $notificacion = $proveedor->notificaciones()->create([
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
        }

        return $notificacion;
    }

}
