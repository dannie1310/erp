<?php

namespace App\Http\Controllers;

use App\Http\Controllers\v1\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacionController;
use App\Models\IGH\Usuario;
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

        $usuario_from = Usuario::where("numero_celular","=",$numero_celular)->first();

        if(!$usuario_from){
            $this->sendWhatsAppMessage("El número celular: ".$numero_celular." no esta asociado a ningún usuario.
			\n Por favor solicite a Soporte a Aplicaciones que le asignen el número.", $from);
        }

        switch($body_ex[0])
        {
            case "AUT":
                if(!key_exists(1, $body_ex))
                {
                    $this->sendWhatsAppMessage("Para autorizar una transacción debe mandar el mensaje con el siguiente formato: AUT ####, donde #### es el identificador de transacción que desea autorizar.", $from);
                }else{
                    if(!is_numeric($body_ex[1])){
                        $this->sendWhatsAppMessage("Para autorizar una transacción debe mandar el mensaje con el siguiente formato: AUT ####, donde #### es el identificador de transacción que desea autorizar.", $from);
                    }
				}



                break;

            case "REC":
                break;

            default:

                $this->sendWhatsAppMessage("La opción que ingresó no es válida.", $from);

                break;
        }

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
        return $client->messages->create($recipient, array('from' => "whatsapp:$twilio_whatsapp_number", 'body' => $message));
    }

}
