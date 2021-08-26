<?php

namespace App\Traits;


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
        $usa_servicio = config('app.env_variables.SERVICIO_CFDI_EN_USO');
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
        }
    }

}
