<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/07/2020
 * Time: 03:26 PM
 */

namespace App\Utils;


class Files
{
    public static $files;
    public static $anidacion="";
    public static $i=0;
    public static $ifil=1;

    public static function getFiles($path){
        if(Files::$i > 0){
            Files::$anidacion .= "____";
        }

        $dir = opendir(public_path($path));
        while ($current = readdir($dir)){
            if( $current != "." && $current != "..") {
                if(is_dir($path.$current)) {
                    Files::$files[Files::$i]["anidacion"] = Files::$anidacion;
                    Files::$files[Files::$i]["nombre"] = $current;
                    Files::$files[Files::$i]["contador"] = "";
                    Files::$files[Files::$i]["clase"] = "fa fa-folder-open";
                    Files::$i +=1;
                    Files::getFiles($path.$current.'/');
                }
                else {
                    Files::$files[Files::$i]["anidacion"] = Files::$anidacion;
                    Files::$files[Files::$i]["nombre"] = $current;
                    Files::$files[Files::$i]["contador"] = Files::$ifil;
                    if (strpos($current,".zip")){
                        Files::$files[Files::$i]["clase"] = "fa fa-file-zip-o";
                    } else if (strpos($current,".xml")){
                        Files::$files[Files::$i]["clase"] = "fa fa-file-code";
                    } else {
                        Files::$files[Files::$i]["clase"] = "fa fa-file";
                    }
                    Files::$ifil +=1;
                    Files::$i +=1;
                }
            }
        }
        return Files::$files;
    }
}