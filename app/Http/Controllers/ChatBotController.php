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
        $body_ex = explode(" ", $body);
        $numero_celular = str_replace("whatsapp:","", $from);

        $peticionService = new PeticionService(new Peticion());

        $respuesta = $peticionService->getRespuesta($request->all());
        $longitud_respuesta = strlen($respuesta);

        $cantidad_cadenas = $longitud_respuesta / 1600;

        for ($i = 0; $i<=$cantidad_cadenas; $i++)
        {
            $cadena = substr($respuesta,$i * 1600, 1600);
            $this->sendWhatsAppMessage($cadena, $from);
        }

        /*$usuario_from = Usuario::where("numero_celular","=",$numero_celular)->first();

        if(!$usuario_from){
            $this->sendWhatsAppMessage("El número celular: ".$numero_celular." no esta asociado a ningún usuario.
			\n Por favor solicite a Soporte a Aplicaciones que le asignen el número enviando un correo a la dirección: soporte_aplicaciones@desarrollo-hi.atlassian.net", $from);
        }

        $H = date_format(now(), "H");
        if($H>11){
            $saludo = "Buenas tardes";
        }else{
            $saludo = "Buenos días";
        }
        $nombre_usuario = ucwords(strtolower($usuario_from->nombre), " ");*/





        /*if(strtoupper(Util::eliminaCaracteresEspeciales($body)) == "ULTIMOS CAMBIOS EN EFOS" || strtoupper($body) == "ULTIMOS_CAMBIOS_EFOS")
        {
            $efos_service = new CtgEfosService(new CtgEfos());
            $this->sendWhatsAppMessage($efos_service->getUltimosCambiosEFOSTXT(), $from);
        }else
        {
            switch(strtoupper($body_ex[0]))
            {
                case "AUT":
                    if(!key_exists(1, $body_ex))
                    {
                        $this->sendWhatsAppMessage("Para autorizar una transacción debe mandar el mensaje con el siguiente formato: AUT ####, donde #### es el identificador de transacción que desea autorizar.", $from);
                        exit;
                    }else{
                        if(!is_numeric($body_ex[1])){
                            $this->sendWhatsAppMessage("Para autorizar una transacción debe mandar el mensaje con el siguiente formato: AUT ####, donde #### es el identificador de transacción que desea autorizar.", $from);
                            exit;
                        }
                    }

                    $transaccionGeneral = Transaccion::find($body_ex[1]);
                    if(!$transaccionGeneral){
                        $this->sendWhatsAppMessage("El identificador de transacción ingresado: ".$body_ex[1]." no existe, favor de verificar.", $from);
                        exit;
                    }
                    $token = Token::where("id_transaccion","=",$transaccionGeneral->id)
                        ->where("id_firmante","=",$usuario_from->firmante->id)->pluck("token")->first();
                    if(!$token){
                        $this->sendWhatsAppMessage("Usted no cuenta con los privilegios necesarios para autorizar la transacción con identificador: ".$transaccionGeneral->id, $from);
                        exit;
                    }

                    $client = new \GuzzleHttp\Client();
                    try {
                        $response = $client->request('GET', "https://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/api/solicitud-pago-anticipado/{$transaccionGeneral->id}/autorizar?access_token={$token}");
                        $responseJSON = json_decode($response->getBody());
                        if ($response->getStatusCode() == 200) {
                            $message = "La transacción ha sido autorizada éxitosamente.";
                            $this->sendWhatsAppMessage($message, $from);
                        } else {
                            $this->sendWhatsAppMessage($responseJSON->message, $from);
                        }
                    } catch (RequestException $th) {
                        $response = json_decode($th->getResponse()->getBody());
                        $this->sendWhatsAppMessage($response->message, $from);
                    }

                    break;

                case "REC":
                    if(!key_exists(1, $body_ex))
                    {
                        $this->sendWhatsAppMessage("Para rechazar una transacción debe mandar el mensaje con el siguiente formato: REC #### MMMMMM, donde #### es el identificador de transacción que desea rechazar y MMMMMM el motivo del rechazo.", $from);
                        exit;
                    }else if(!is_numeric($body_ex[1])){
                        $this->sendWhatsAppMessage("Para rechazar una transacción debe mandar el mensaje con el siguiente formato: REC #### MMMMMM, donde #### es el identificador de transacción que desea rechazar y MMMMMM el motivo del rechazo.", $from);
                        exit;
                    } else if(!key_exists(2, $body_ex))
                    {
                        $this->sendWhatsAppMessage("Para rechazar una transacción debe mandar el mensaje con el siguiente formato: REC #### MMMMMM, donde #### es el identificador de transacción que desea rechazar y MMMMMM el motivo del rechazo.", $from);
                        exit;
                    }

                    $motivo = "";

                    for ($i = 2 ; $i<count($body_ex); $i++) {
                        $motivo .= $body_ex[$i]." ";
                    }

                    $transaccionGeneral = Transaccion::find($body_ex[1]);
                    if(!$transaccionGeneral){
                        $this->sendWhatsAppMessage("El identificador de transacción ingresado: ".$body_ex[1]." no existe, favor de verificar.", $from);
                        exit;
                    }
                    $token = Token::where("id_transaccion","=",$transaccionGeneral->id)
                        ->where("id_firmante","=",$usuario_from->firmante->id)->pluck("token")->first();
                    if(!$token){
                        $this->sendWhatsAppMessage("Usted no cuenta con los privilegios necesarios para rechazar la transacción con identificador: ".$transaccionGeneral->id, $from);
                        exit;
                    }

                    $client = new \GuzzleHttp\Client();
                    try {
                        $response = $client->request('GET', "https://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/api/solicitud-pago-anticipado/{$transaccionGeneral->id}/rechazar?access_token={$token}&motivo={$motivo}");
                        $responseJSON = json_decode($response->getBody());
                        if ($response->getStatusCode() == 200) {
                            $message = "La transacción ha sido rechazada éxitosamente.";
                            $this->sendWhatsAppMessage($message, $from);
                        } else {
                            $this->sendWhatsAppMessage($responseJSON->message, $from);
                        }
                    } catch (RequestException $th) {
                        $response = json_decode($th->getResponse()->getBody());
                        $this->sendWhatsAppMessage($response->message, $from);
                    }

                    break;

                CASE "DEL":

                    $this->sendWhatsAppMessage("Por el momento esta opción no esta disponible.", $from);

                    break;

                CASE "ULTIMOS_CAMBIOS_EFOS":

                    $efos_service = new CtgEfosService(new CtgEfos());
                    $this->sendWhatsAppMessage($efos_service->getUltimosCambiosEFOSTXT(), $from);
                    break;

                default:

                    $this->sendWhatsAppMessage($saludo." ".$nombre_usuario.", las peticiones disponibles por el momento son:

-_*Últimos Cambios en EFOS*_: Para conocer los últimos cambios en el estado de los EFOS puede mandar alguno de los siguientes mensajes:
\n-Últimos cambios en EFOS, \n-ULTIMOS_CAMBIOS_EFOS

-_*Autorizar Transacción*_: Para autorizar una transacción debe mandar el mensaje con el siguiente formato: AUT ####, donde #### es el identificador de transacción que desea autorizar.

-_*Rechazar Transacción*_: Para rechazar una transacción debe mandar el mensaje con el siguiente formato: REC #### MMMMMM, donde #### es el identificador de transacción que desea rechazar y MMMMMM el motivo del rechazo.

", $from);

                    break;
            }

        }*/



        /*$client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', "https://api.github.com/users/$body");
            $githubResponse = json_decode($response->getBody());
            if ($response->getStatusCode() == 200) {
                $message = "*Name:* $githubResponse->name\n";
                $message .= "*Bio:* $githubResponse->bio\n";
                $message .= "*Lives in:* $githubResponse->location\n";
                $message .= "*Number of Repos:* $githubResponse->public_repos\n";
                $message .= "*Followers:* $githubResponse->followers devs\n";
                $message .= "*Following:* $githubResponse->following devs\n";
                $message .= "*URL:* $githubResponse->html_url\n";
                $this->sendWhatsAppMessage($message, $from);
            } else {
                $this->sendWhatsAppMessage($githubResponse->message, $from);
            }
        } catch (RequestException $th) {
            $response = json_decode($th->getResponse()->getBody());
            $this->sendWhatsAppMessage($response->message, $from);
        }*/



        return;
    }

    /**
     * Sends a WhatsApp message  to user using
     * @param string $message Body of sms
     * @param string $recipient Number of recipient
     */
    public function sendWhatsAppMessage(string $message, string $recipient)
    {
        /*$twilio_whatsapp_number = getenv('TWILIO_WHATSAPP_NUMBER');
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");*/

        $twilio_whatsapp_number = config('app.env_variables.TWILIO_WHATSAPP_NUMBER');
        $account_sid = config('app.env_variables.TWILIO_SID');
        $auth_token = config('app.env_variables.TWILIO_AUTH_TOKEN');

        $client = new Client($account_sid, $auth_token);
        return $client->messages->create(
            $recipient,
            array(
                'from' => "whatsapp:$twilio_whatsapp_number",
                'body' => $message
            )
        );
    }

}
