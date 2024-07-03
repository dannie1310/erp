<?php

namespace App\Providers;

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

        'App\Events\EnvioIngresoFactura' => [
            'App\Listeners\SendIngresaFacturaNotification',
        ],

        'App\Events\AperturaInvitacion' => [
            'App\Listeners\SendAperturaInvitacionNotification',
        ],

        'App\Events\CambioFechaCierreInvitacion' => [
            'App\Listeners\SendCambioFechaCierreInvitacionNotification',
        ],

        'App\Events\EnvioPresupuesto' => [
            'App\Listeners\SendPresupuestoEnviadoNotification',
        ],

        'Illuminate\Notifications\Events\NotificationSent' => [
            'App\Listeners\LogNotification',
        ],

        'App\Events\SolicitudAutorizacionPagoAnticipado' => [
            'App\Listeners\SendSolicitudPagoAnticipadoNotification',
            'App\Listeners\SendSolicitudPagoAnticipadoNotificationParaAutorizacion',
            'App\Listeners\SendSolicitudPagoAnticipadoNotificationSMS',
        ],

        'App\Events\SolicitudAutorizacionPagoAnticipadoSinContexto' => [
            'App\Listeners\SendSolicitudPagoAnticipadoNotificationParaAutorizacionSinContexto',
        ],

        'App\Events\AutorizacionPagoAnticipado' => [
            'App\Listeners\SendAutorizacionPagoAnticipadoNotification',
        ],

        'App\Events\RechazoPagoAnticipado' => [
            'App\Listeners\SendRechazoPagoAnticipadoNotification',
        ],

        'App\Events\RegistroNotificacionREP' => [
            'App\Listeners\SendNotificacionREPNotification',
            'App\Listeners\GuardaComunicadoNotificacionREP',
        ],
        'App\Events\Concursos\FinalizacionDeAperturaConcurso' => [
            'App\Listeners\Concursos\SendAperturaConcursoNotification',
            'App\Listeners\Concursos\SendAperturaConcursoNotificationWA'
        ],
        'App\Events\Concursos\InicioDeAperturaConcurso' => [
            'App\Listeners\Concursos\SendInicioAperturaConcursoNotificationWA'
        ],
        'App\Events\Concursos\ActualizacionDatosAperturaConcurso' => [
            'App\Listeners\Concursos\SendActualizacionConcursoNotificationWA'
        ],
        'App\Events\Concursos\RegistroFalloConcurso' => [
            'App\Listeners\Concursos\SendRegistroFalloConcursoNotificationWA'
        ],
        'App\Events\IFS\EnvioXMLDocumentoRecursos' => [
            'App\Listeners\IFS\SendXMLDocumentoRecursosNotification',
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
