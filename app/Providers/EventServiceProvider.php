<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\IncidenciaCI' => [
            'App\Listeners\SendIncidenciaCINotification',
        ],

        'App\Events\CambioEFOS' => [
            'App\Listeners\SendCambiosEFOSNotification',
        ],

        'App\Events\FinalizaProcesamientoLoteBusquedas' => [
            'App\Listeners\SendFinalizaProcesamientoLoteBusquedaNotification',
            ],
        'App\Events\FinalizaCargaCFD' => [
            'App\Listeners\SendFinalizaCargaCFDNotification',
        ],

        'App\Events\RegistroSolicitudRecepcionCFDI' => [
            'App\Listeners\SendRegistroSolicitudRecepcionCFDINotification',
        ],

        'App\Events\FinalizaProcesamientoAsociacion' => [
            'App\Listeners\SendFinalizaAsociacionPolizaCFDINotification',
        ],

        'App\Events\AprobacionSolicitudRecepcionCFDI' => [
            'App\Listeners\SendAprobacionSolicitudRecepcionCFDINotification',
        ],

        'App\Events\RechazoSolicitudRecepcionCFDI' => [
            'App\Listeners\SendRechazoSolicitudRecepcionCFDINotification',
        ],

        'App\Events\CancelacionSolicitudRecepcionCFDI' => [
            'App\Listeners\SendCancelacionSolicitudRecepcionCFDINotification',
        ],

        'App\Events\CambioNoLocalizados' => [
            'App\Listeners\SendCambiosNoLocalizadosNotification',
        ],

        'App\Events\RegistroUsuarioProveedor' => [
            'App\Listeners\SendCredencialesAccesoNotification',
        ],

        'App\Events\RegistroInvitacion' => [
            'App\Listeners\SendInvitacionCotizarNotification',
        ],

        'App\Events\ActualizacionClaveUsuarioProveedor' => [
            'App\Listeners\SendCredencialesAccesoNotification',
        ],

        'Illuminate\Mail\Events\MessageSent' => [
            'App\Listeners\LogSentMessage',
        ],

        'App\Events\EnvioCotizacion' => [
            'App\Listeners\SendCotizacionEnviadaNotification',
        ],

        'App\Events\AperturaInvitacion' => [
            'App\Listeners\SendAperturaInvitacionNotification',
        ],

        'Illuminate\Notifications\Events\NotificationSent' => [
            'App\Listeners\LogNotification',
        ],

            Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
