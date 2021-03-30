<?php

namespace App\Escom\Lib;

class Date {

    public static function formato($str, $reg){
        if($str){
            $a = explode('-',$str);
            if(checkdate($a[1],$a[2],$a[0])){
                if($reg != ''){
            
                    $meses = array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio',
                                    '07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
                    
                    $buscar = ['D','d','A','a','MMM','mmm','MM','mm','Mm','X','x'];
                    $reemplazar = [
                        str_pad($a[2],2,'0',STR_PAD_LEFT),
                        $a[2],
                        $a[0],
                        substr($a[0],2,2),
                        strtoupper(substr($meses[$a[1]],0,3)),   
                        strtolower(substr($meses[$a[1]],0,3)),   
                        strtoupper($meses[$a[1]]),    
                        strtolower($meses[$a[1]]),    
                        $meses[$a[1]],    
                        str_pad($a[1],2,'0'),  
                        $a[1]
                    ];

                    return str_replace($buscar, $reemplazar, $reg);

                }else{
                    return $str;
                }
            }else{
                return 'N/A';
            }
        }else{
            return 'N/A';
        }
    }
} 