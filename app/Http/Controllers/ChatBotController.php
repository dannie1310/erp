<?php

namespace App\Http\Controllers;

use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\ChatBot\Peticion;
use App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\Token;
use App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\Transaccion;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Services\SEGURIDAD_ERP\ChatBot\PeticionService;
use App\Services\SEGURIDAD_ERP\Finanzas\CtgEfosService;
use App\Utils\Util;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
use Twilio\Rest\Client;

class ChatBotController extends Controller
{
    public function listenToReplies(Request $request)
    {
        $from = $request->input('From');
        $body = $request->input('Body');

        $peticionService = new PeticionService(new Peticion());

        $respuesta = $peticionService->getRespuesta($request->all());

        if(is_array($respuesta))
        {
            $this->sendWhatsAppMessage(
                $respuesta["body"],
                $from,
                $respuesta["mediaUrl"]
            );
        }
        else {
            $longitud_respuesta = strlen($respuesta);
            $cantidad_cadenas = $longitud_respuesta / 1600;

            for ($i = 0; $i<=$cantidad_cadenas; $i++)
            {
                $cadena = substr($respuesta,$i * 1600, 1600);
                $this->sendWhatsAppMessage($cadena, $from);
            }
        }
    }

    /**
     * Sends a WhatsApp message  to user using
     * @param string $message Body of sms
     * @param string $recipient Number of recipient
     */
    public function sendWhatsAppMessage(string $message, string $recipient, string $mediaUrl = null)
    {

        $twilio_whatsapp_number = config('app.env_variables.TWILIO_WHATSAPP_NUMBER');
        $account_sid = config('app.env_variables.TWILIO_SID');
        $auth_token = config('app.env_variables.TWILIO_AUTH_TOKEN');

        $client = new Client($account_sid, $auth_token);
        if($mediaUrl){
            $parametros = array(
                'from' => "whatsapp:$twilio_whatsapp_number",
                'body' => $message,
                'mediaUrl' => $mediaUrl,
            );
        }else{
            $parametros = array(
                'from' => "whatsapp:$twilio_whatsapp_number",
                'body' => $message,
            );
        }

        return $client->messages->create(
            $recipient,
            $parametros
        );
    }

}
