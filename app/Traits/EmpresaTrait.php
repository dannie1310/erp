<?php

namespace App\Traits;


use App\Events\IncidenciaCI;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Views\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaVw;

trait EmpresaTrait
{
    public function validaRFC($rfc)
    {
        if(!$this->validaRFCSM($rfc)){
            abort(500,"El RFC: ".$rfc.", no es válido ante el SAT");
        }
    }

    public function validaRFCSM($rfc)
    {
        /*$usa_servicio = config('app.env_variables.SERVICIO_CFDI_EN_USO');
        if ($usa_servicio == 1) {
            $client = new \GuzzleHttp\Client();
            $url = config('app.env_variables.SERVICIO_RFC_URL');
            $token = config('app.env_variables.SERVICIO_CFDI_TOKEN');

            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ];
            try{
                $client->request('GET', $url."".$rfc, [
                    'headers' => $headers,
                ]);
            } catch (\Exception $e){
                return false;
            }
            return true;
        } else {
            return true;
        }*/
        $this->rfcValidaEfos($rfc);
    }

    public function rfcValido($rfc)
    {
        if(strlen(str_replace(" ","", $rfc))>0){
            $reg_exp = "/^(([A-ZÑ&]{3,4})[\-]?([0-9]{2})([0][13578]|[1][02])(([0][1-9]|[12][\\d])|[3][01])[\-]?([A-V1-9]{1})([A-Z1-9]{1})([A0-9]{1}))|".
                "(([A-ZÑ&]{3,4})[\-]?([0-9]{2})([0][13456789]|[1][012])(([0][1-9]|[12][\\d])|[3][0])[\-]?([A-V1-9]{1})([A-Z1-9]{1})([A0-9]{1}))|".
                "(([A-ZÑ&]{3,4})[\-]?([02468][048]|[13579][26])[0][2]([0][1-9]|[12][\\d])[\-]?([A-V1-9]{1})([A-Z1-9]{1})([A0-9]{1}))|".
                "(([A-ZÑ&]{3,4})[\-]?([0-9]{2})[0][2]([0][1-9]|[1][0-9]|[2][0-8])[\-]?([A-V1-9]{1})([A-Z1-9]{1})([A0-9]{1}))$/";
            return (bool)preg_match($reg_exp, $rfc);
        }
        return true;
    }

    public function rfcValidaBoletinados($rfc)
    {
        $boletinada = EmpresaBoletinadaVw::where("rfc","=",$rfc)->first();
        if($boletinada)
        {
            abort(403, 'La empresa con RFC: '.$rfc.' esta boletinada para HI por '.$boletinada->motivo_txt.', no se pueden tener operaciones con esta empresa.

             Favor de comunicarse con el área de administración del corporativo para cualquier aclaración.');
        }

    }

    public function rfcValidaEfos($rfc)
    {
        $EFOSD = CtgEfos::where("rfc","=",$rfc)->where('estado', 0)->first();
        $EFOSP = CtgEfos::where("rfc","=",$rfc)->where('estado', 2)->first();
        if(!is_null($EFOSD))
        {
            event(new IncidenciaCI(
                ["id_tipo_incidencia"=>1,
                    "rfc"=>$rfc,
                    "empresa"=>$EFOSD->razon_social,
                ]
            ));
            abort(403, 'La empresa con RFC: '.$rfc.' esta invalidada por el SAT por ser un EFOS definitivo, no se pueden tener operaciones con esta empresa.

             Favor de comunicarse con el área fiscal para cualquier aclaración.');
        }else if(!is_null($EFOSP))
        {
            event(new IncidenciaCI(
                ["id_tipo_incidencia"=>2,
                    "rfc"=>$rfc,
                    "empresa"=>$this->efo->razon_social,
                ]
            ));
            abort(403, 'La empresa con RFC: '.$rfc.' esta invalidada por el SAT por ser un EFOS presunto, no se pueden tener operaciones con esta empresa.

             Favor de comunicarse con el área fiscal para cualquier aclaración.');
        }
    }

}
