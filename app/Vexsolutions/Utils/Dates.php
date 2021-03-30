<?php

namespace Utils;

class Dates
{


    public static function getDays (){

        $days = array();
        for($c=1; $c<=31;$c++){
            $dia = str_pad($c,2,"0",STR_PAD_LEFT);
            $days[] = array('id'=>$dia, 'text'=>$dia);
        }

        return $days;
    }


    public static function getMonths () {

        $meses = array(
            array('id'=>'01', 'text'=> __('glovo.months.01') ),
            array('id'=>'02', 'text'=> __('glovo.months.02')),
            array('id'=>'03', 'text'=> __('glovo.months.03')),
            array('id'=>'04', 'text'=> __('glovo.months.04')),
            array('id'=>'05', 'text'=> __('glovo.months.05')),
            array('id'=>'06', 'text'=> __('glovo.months.06')),
            array('id'=>'07', 'text'=> __('glovo.months.07')),
            array('id'=>'08', 'text'=> __('glovo.months.08')),
            array('id'=>'09', 'text'=> __('glovo.months.09')),
            array('id'=>'10', 'text'=> __('glovo.months.10')),
            array('id'=>'11', 'text'=> __('glovo.months.11')),
            array('id'=>'12', 'text'=> __('glovo.months.12')),
        );

        return $meses;

    }


    // Transform minutes like "105" into hours like "1:45".
    public static function minutesToHours($time, $format = '%02d:%02d')
    {
        if ($time < 1) {
            return;
        }

        $hours = floor($time / 60);
        $minutes = ($time % 60);

        return sprintf($format, $hours, $minutes);
    }

}
