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
    protected static $files;
    protected static $anidacion="";
    protected static $i=0;
    protected static $ifil=1;

    public static function getFiles($path){
        if(self::$i > 0){
            self::$anidacion .= "____";
        }

        $dir = opendir(public_path($path));
        while ($current = readdir($dir)){
            if( $current != "." && $current != "..") {
                if(is_dir($path.$current)) {
                    self::$files[self::$i]["anidacion"] = self::$anidacion;
                    self::$files[self::$i]["nombre"] = $current;
                    self::$files[self::$i]["contador"] = "";
                    self::$files[self::$i]["clase"] = "fa fa-folder-open";
                    self::$i +=1;
                    self::getFiles($path.$current.'/');
                }
                else {
                    self::$files[self::$i]["anidacion"] = self::$anidacion;
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
}