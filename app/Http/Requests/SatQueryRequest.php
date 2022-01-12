<?php


namespace App\Http\Requests;


class SatQueryRequest
{

    public static function soapRequest($rfcEmisor, $rfcReceptor, $total, $uuid, $sello)
    {
        $url = config('app.env_variables.SERVICIO_CFDI_URL');

        $xml_post_string = self::getSoapBody($rfcEmisor, $rfcReceptor, $total, $uuid, $sello);
        $headers = self::headers($xml_post_string);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 50000);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        set_time_limit(0);
        $soap = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            throw new \Exception("cURL Error #:" . $err);
        } else {
            return self::response(self::xml2array($soap));
        }
    }

    public static function getSoapBody($rfcEmisor, $rfcReceptor, $total, $uuid, $sello)
    {
        return '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
                   <soapenv:Header/>
                   <soapenv:Body>
                      <tem:Consulta>
                          <tem:expresionImpresa><![CDATA[?re=' . $rfcEmisor . '&rr=' . $rfcReceptor . '&tt=' . $total . '&id=' . $uuid . '&fe=' . $sello .']]></tem:expresionImpresa>
                      </tem:Consulta>
                   </soapenv:Body>
                </soapenv:Envelope>';
    }

    public static function headers($xml_post_string)
    {
        return array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: http://tempuri.org/IConsultaCFDIService/Consulta",
            "Content-length: " . strlen($xml_post_string),
        );
    }

    public static function xml2array($xml)
    {
        return json_decode(json_encode(simplexml_load_string(str_replace("s:", "", str_replace("a:", "", str_replace("i:", "", '<?xml version="1.0" encoding="utf-8"?>' . $xml))))), TRUE);
    }

    public static function response($data)
    {
        if( $data["Body"]["ConsultaResponse"]["ConsultaResult"]["Estado"] != 'Vigente' && $data["Body"]["ConsultaResponse"]["ConsultaResult"]["CodigoEstatus"] != 'S - Comprobante obtenido satisfactoriamente.') {
            if($data["Body"]["ConsultaResponse"]["ConsultaResult"]["EstatusCancelacion"] != [])
            {
                abort(500, "Aviso SAT: \n".$data["Body"]["ConsultaResponse"]["ConsultaResult"]["CodigoEstatus"]."\nEstado: ".$data["Body"]["ConsultaResponse"]["ConsultaResult"]["Estado"] ."\nEs cancelable: ". $data["Body"]["ConsultaResponse"]["ConsultaResult"]["EsCancelable"]."\nEstado de cancelación: ".$data["Body"]["ConsultaResponse"]["ConsultaResult"]["EstatusCancelacion"]);
            }else {
                abort(500, "Aviso SAT: \n" . $data["Body"]["ConsultaResponse"]["ConsultaResult"]["CodigoEstatus"] . "\nEstado: " . $data["Body"]["ConsultaResponse"]["ConsultaResult"]["Estado"] ."\nEs cancelable: ". $data["Body"]["ConsultaResponse"]["ConsultaResult"]["EsCancelable"]);
            }
        }
    }
}
