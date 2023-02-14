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

    public function guardarContactos($id, $destinatarios)
    {
        $proveedor = $this->show($id);
        foreach ($destinatarios as $destinatario)
        {
            $proveedor->getContacto($destinatario);
        }
    }

    public function registrarNotificacion($id, $destinatarios)
    {
        $proveedor = $this->show($id);

        $notificacion = $proveedor->notificaciones()->create([
        ]);
        if($notificacion)
        {
            foreach ($destinatarios as $destinatario)
            {
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
