<?php

final class HelperData{
    
    /**
     * Exibe um valor em bytes
     * @param float $filesize
     * @return string
     */
    public static function getFileSize($filesize){
        $unit = ' Bytes';
        if($filesize > FILE_SIZE_KILO AND $filesize <= FILE_SIZE_MEGA){
            $filesize = $filesize / FILE_SIZE_KILO;
            $unit = ' Kb';
        }elseif ($filesize > FILE_SIZE_MEGA AND $filesize <= FILE_SIZE_GIGA){
            $filesize = $filesize / FILE_SIZE_MEGA;
            $unit = ' Mb';
        }elseif ($filesize > FILE_SIZE_GIGA){
            $filesize = $filesize / FILE_SIZE_GIGA;
            $unit = ' Gb';
        }
        
        return number_format($filesize, 2, ',', '.').$unit;
    }
    
    /**
     * Exibe um valor como moeda corrente (R$)
     * @param float $valor
     * @return string
     */
    public static function printLikeMoney($valor){
        $valor = ((is_null($valor) OR empty($valor)) ? 0 : $valor);
        return 'R$ '.number_format($valor, 2, ',', '.');
    }
    
    public static function removeAccents($string){
        $search = array(
            'a'=>'/á|à|ã|â|ä/',
            'A'=>'/Á|À|Ã|Â|Ä/',
            'e'=>'/é|è|ê|ë/',
            'E'=>'/É|È|Ê|Ë/',
            'i'=>'/í|ì|ï|î/',
            'I'=>'/Í|Ì|Ï|Î/',
            'o'=>'/ó|ò|ô|õ|ö/',
            'O'=>'/Ó|Ò|Õ|Ô|Ö/',
            'u'=>'/ú|ù|û|ü/',
            'U'=>'/Ú|Ù|Û|Ü/',
            'c'=>'/ç/',
            'C'=>'/Ç/'
        );
        
        foreach ($search as $ltr=>$accent)
            $string = preg_replace($accent, $ltr, $string);
        
        return $string;
    }
    
}