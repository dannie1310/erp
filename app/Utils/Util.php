<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 23/10/2019
 * Time: 12:03 PM
 */

namespace App\Utils;


class Util
{
    public static function eliminaPalabrasComunes($string)
    {
        $string = str_replace(
            array('.',',',' SAPI',' SA', ' DE ', ' CV', ' S RL',' SRL',' SR L',' S C'," SC", " Y "," S A C V", " D ECV", " (COMPLEMENTARIA)"," (USD)"," (DOLARES)", "(COMPLEMENTARIA)","(USD)","(DOLARES)"," (COMPLE)","(COMPLE)"),
            array('','','','', ' ','', '','', '','','',' ',"","","","","","","","",""),
            $string
        );
        return mb_strtoupper(trim($string));
    }

    function elimina_caracteres_especiales($string){
        //echo $string;
        //$string = trim($string);
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ã', 'å', 'ª', 'Á', 'À', 'Â', 'Ä', 'Å', 'Ã', 'Æ'),
            array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A', 'A', 'A'),
            $string
        );
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string    );
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'ð', 'õ', 'ø', 'Ó', 'Ò', 'Ö', 'Ô', 'Õ', 'Ø'),
            array('o', 'o', 'o', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'O', 'O'),
            $string
        );
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç', 'Ð' ,'Ý', 'æ', 'ý', 'ÿ', 'Ÿ', 'Š', 'š'),
            array('n', 'N', 'c', 'C', 'D', 'Y', 'e', 'y', 'y', 'Y', 'S', 's'),
            $string
        );
        $string = str_replace(
            array('&'),
            array('y'),
            $string
        );
        //     //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "`", "]",
                "+", "}", "{", "¨", "´",
                ">", "<", ";", ",", ":",
                ".", "=", "`", "¢", "£",
                "¤", "¥", "¦", "§", "¨",
                "©", "ª", "«", "¬", "®",
                "¯", "°", "±", "²", "³",
                "´", "µ", "¶", "·", "¸",
                "¹", "º", "»", "¼", "½",
                "¾", "¿", "×", "Þ", "ß",
                "÷", "þ", "Œ", "œ", "ƒ",
                "–", "—", "‘", "’", "‚",
                "“", "”", "„", "†", "‡",
                "•", "…", "‰", "€", "™",
                "ￃﾩ"
            ),
            '',
            $string
        );

        return preg_replace( "/\r|\n/", " ", $string );
    }

    public static function eliminaCaracteresEspeciales($string)
    {
        //echo $string;
        //$string = trim($string);
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ã', 'å', 'ª', 'Á', 'À', 'Â', 'Ä', 'Å', 'Ã', 'Æ'),
            array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A', 'A', 'A'),
            $string
        );
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string    );
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'ð', 'õ', 'ø', 'Ó', 'Ò', 'Ö', 'Ô', 'Õ', 'Ø'),
            array('o', 'o', 'o', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'O', 'O'),
            $string
        );
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç', 'Ð' ,'Ý', 'æ', 'ý', 'ÿ', 'Ÿ', 'Š', 'š'),
            array('n', 'N', 'c', 'C', 'D', 'Y', 'e', 'y', 'y', 'Y', 'S', 's'),
            $string
        );
        $string = str_replace(
            array('&'),
            array('y'),
            $string
        );
        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "`", "]",
                "+", "}", "{", "¨", "´",
                ">", "<", ";", ",", ":",
                ".", "=", "`", "¢", "£",
                "¤", "¥", "¦", "§", "¨",
                "©", "ª", "«", "¬", "®",
                "¯", "°", "±", "²", "³",
                "´", "µ", "¶", "·", "¸",
                "¹", "º", "»", "¼", "½",
                "¾", "¿", "×", "Þ", "ß",
                "÷", "þ", "Œ", "œ", "ƒ",
                "–", "—", "‘", "’", "‚",
                "“", "”", "„", "†", "‡",
                "•", "…", "‰", "€", "™",
                "ￃﾩ", "*"
            ),
            '',
            $string
        );

        return preg_replace( "/\r|\n/", " ", $string );
    }

}
