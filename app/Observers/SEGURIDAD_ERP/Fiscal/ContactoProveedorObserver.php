<?php
namespace App\Observers\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\ContactoProveedorREP;
use App\Models\SEGURIDAD_ERP\Fiscal\RepNotificaciones;
use App\Notifications\NotificacionREP;

class ContactoProveedorObserver
{
    /**
     * @param ContactoProveedorREP $contacto
     * @return void
     */
    public function creating(ContactoProveedorREP $contacto)
    {
        $contacto->usuario_registro = auth()->id();
    }
}
