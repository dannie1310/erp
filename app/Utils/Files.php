<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/07/2020
 * Time: 03:26 PM
 */

namespace App\Utils;


use Chumper\Zipper\Zipper;

class Files
{
    protected static $files;
    protected static $anidacion="";
    protected static $i=0;
    protected static $ifil=1;

    public static function getFiles($path, $anidacion_padre = ""){
        if(self::$i > 0){
            $anidacion_padre .= "____";
        }

        $dir = opendir(public_path($path));
        while ($current = readdir($dir)){
            if( $current != "." && $current != "..") {
                if(is_dir($path.$current)) {
                    self::$files[self::$i]["anidacion"] = $anidacion_padre;
                    self::$files[self::$i]["nombre"] = $current;
                    self::$files[self::$i]["contador"] = "";
                    self::$files[self::$i]["clase"] = "fa fa-folder-open";
                    self::$i +=1;
                    self::getFiles($path.$current.'/',$anidacion_padre);
                }
                else {
                    self::$files[self::$i]["anidacion"] = $anidacion_padre;
                    self::$files[self::$i]["nombre"] = $current;
                    self::$files[self::$i]["contador"] = self::$ifil;
                    if (strpos($current,".zip")){
                        self::$files[self::$i]["clase"] = "fa fa-file-zip-o";
                    } else if (strpos($current,".xml")){
                        self::$files[self::$i]["clase"] = "fa fa-file-code";
                    } else {
                        self::$files[self::$i]["clase"] = "fa fa-file";
                    }
                    self::$ifil +=1;
                    self::$i +=1;
                }
            }
        }
        return self::$files;
    }



    public static function extraeZIPOriginal($ruta_origen, $ruta_destino = "")
    {
        if($ruta_destino == ""){
            $ruta_destino = substr($ruta_origen,0,strlen($ruta_origen)-4);
        }
        try{
            $zipper = new Zipper();
            $zipper->make(public_path($ruta_origen))->extractTo(public_path($ruta_destino));
            $zipper->close();
            $zipper->delete();
        }catch (\Exception $e){
            abort(500, "Hubo un error al extraer el archivo zip proporcionado. Ruta Origen: ".$ruta_origen . 'Ruta Destino: '.$ruta_destino.' Ln.' . $e->getLine() . ' ' . $e->getMessage());
        }
    }

    public static function extraeZIP($ruta_origen, $ruta_destino = "")
    {
        if($ruta_destino == ""){
            $ruta_destino = substr($ruta_origen,0,strlen($ruta_origen)-4);
        }
        try{
            $zip = new \ZipArchive();
            if ($zip->open($ruta_origen) === TRUE) {
                $zip->extractTo($ruta_destino);
                $zip->close();
                unlink($ruta_origen);

            }
        }catch (\Exception $e){
            abort(500, "Hubo un error al extraer el archivo zip proporcionado. Ruta Origen: ".$ruta_origen . 'Ruta Destino: '.$ruta_destino.' Ln.' . $e->getLine() . ' ' . $e->getMessage());
        }


    }
}