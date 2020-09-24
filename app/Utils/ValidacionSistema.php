<?php


namespace App\Utils;


class ValidacionSistema
{
    private $deposito_claves;

    public function __construct()
    {
        $this->deposito_claves = config('app.env_variables.DKEY');
    }

    function encripta($texto_plano) {

        $base = 1024;
        $restar = 88;
        $lon = 2048;
        $car = $lon - ($restar * ($lon/$base));
        $texto_plano = substr($texto_plano, 0, $car/8) ;
        // $llave_publica = openssl_pkey_get_public("file://" . $this->deposito_claves . "certificado.pem");
        $llave_publica = openssl_pkey_get_public("file://" . $this->deposito_claves . "SAO_certificado$lon.crt");
        openssl_public_encrypt($texto_plano, $texto_encriptado, $llave_publica);
        $texto_encriptado = base64_encode($texto_encriptado);
        return $texto_encriptado;
    }

    function desencripta($texto_encriptado) {

        $texto_encriptado = base64_decode($texto_encriptado);
        // $llave_privada = openssl_pkey_get_private("file://" . $this->deposito_claves . "privkey.pem", "sao01022013#");
        $llave_privada = openssl_pkey_get_private("file://" . $this->deposito_claves . "SAO_privada1024.key", "sao01022013#");
        openssl_private_decrypt($texto_encriptado, $texto_desencriptado, $llave_privada);
        if($texto_desencriptado=="" || $texto_desencriptado == null){
            $llave_privada = openssl_pkey_get_private("file://" . $this->deposito_claves . "SAO_privada2048.key", "sao01022013#");
            openssl_private_decrypt($texto_encriptado, $texto_desencriptado, $llave_privada);
            if($texto_desencriptado=="" || $texto_desencriptado == null){
                $llave_privada = openssl_pkey_get_private("file://" . $this->deposito_claves . "SAO_privada4096.key", "sao01022013#");
                openssl_private_decrypt($texto_encriptado, $texto_desencriptado, $llave_privada);
            }
        }

        /*$llave_privada = openssl_pkey_get_private("file://" . $this->deposito_claves . "SAO_privada4096.key", "sao01022013#");
            openssl_private_decrypt($texto_encriptado, $texto_desencriptado, $llave_privada);*/
        return ($texto_desencriptado);
    }

}
