<?php

namespace app\Library\lib;


class EMLib {




    public static function route($route, $rowid, $doAction){

        //ruta del formulario y la accion que ejecutara
        # POST  usuarios/insert     -> Creara un nuevo registro
        # POST  usuarios/{id}/edit  -> Modificara un registro
        # POST  usuarios/{id}/delete->

        if ($doAction=='insert'){
            $route   = "{$route}/save";
        }elseif($doAction=='save'){
            $route   = "{$route}/save";
        }elseif($doAction=='edit'){
            $route   = "{$route}/{$rowid}/edit";
        }elseif($doAction=='detail'){
            $route   = "{$route}/{$rowid}";
        }elseif($doAction=='delete'){
            $route   = "{$route}/{$rowid}/delete";
        }else{

            if( $rowid){
                $route   = "{$route}/{$rowid}/{$doAction}";
            }else{
                $route   = "{$route}/{$doAction}";
            }

        }

        return $route;
    }


    /**
     * Crea una version similar a la del primer controlador de ruta pero con los siguientes parametros de salida
     * POST  usuarios/save       -> Creara un nuevo registro
     * POST  usuarios/edit/34    -> Modificara un registro
     * POST  usuarios/delete/34  -> Borra un registro
     * POST  usuarios/action/34  -> Realiza una accion determinada
     *
     * @param $route
     * @param $rowid
     * @param $doAction
     * @return string
     *
     * @see route
     */
    public static function routeController($route, $rowid, $doAction, $postController=true){


        if( substr($route, -1) == '/') {
            $route = substr($route, 0, -1);
        }

        if( $postController){
            return $route.'/do-post';
        }


        //ruta del formulario y la accion que ejecutara

        if ($doAction=='01'){
            $route   = "{$route}/do-post";
        }elseif($doAction=='save'){
            $route   = "{$route}/do-post";
        }elseif($doAction=='02'){
            $route   = "{$route}/do-post";
        }elseif($doAction=='03'){
            $route   = "{$route}/detalle";
        }elseif($doAction=='05'){
            $route   = "{$route}/do-post";
        }else{

            #para renombrar metodos y reques diferentes a los anteriores
            if( $rowid and $doAction){
                $route   = "{$route}/{$doAction}/{$rowid}";
            }elseif($rowid){
                $route   = "{$route}/{$rowid}";
            }else{
                $route   = "{$route}/{$doAction}";
            }

        }

        return $route;
    }


    public static function getAnios($start){
        if(!$start) $start = date('2000');

        $end = (int) date('Y');

        $anios = array();
        for ($c = $end; $c >= $start; $c--){
            $anios[$c] = $c;
        }

        return $anios;

    }

    public static function getMeses(){
        $mes = array(
            '01' => 'ENERO',
            '02' => 'FEBRERO',
            '03' => 'MARZO',
            '04' => 'ABRIL',
            '05' => 'MAYO',
            '06' => 'JUNIO',
            '07' => 'JULIO',
            '08' => 'AGOSTO',
            '09' => 'SEPTIEMBRE',
            '10' => 'OCTUBRE',
            '11' => 'NOVIEMBRE',
            '12' => 'DICIEMBRE'
        );

        return $mes;

    }

    public static function getDias(){
        $dias = array();
        for($c = 1; $c <= 31; $c++){
            $d = str_pad($c,2,'0',STR_PAD_LEFT);
            $dias[$c] = $c;
        }
        return $dias;
    }


    public static function getGenero(){
        return ['M'=>'MASCULINO', 'F'=>'FEMENINO','N'=>'NO IDENTIFICADO'];
    }

    public static function elipsis($text, $len=10){
        $text   = strlen($text) > $len ? substr($text,0,$len)."..." : $text;
        return $text;
    }


    public static function titleCase($string)
    {
        $word_splitters = array(' ', '-', "O'", "L'", "D'", 'St.', 'Mc');
        $lowercase_exceptions = array('the', 'van', 'den', 'von', 'und', 'der', 'de', 'da', 'of', 'and', "l'", "d'");
        $uppercase_exceptions = array('III', 'IV', 'VI', 'VII', 'VIII', 'IX');

        $string = strtolower($string);
        foreach ($word_splitters as $delimiter)
        {
            $words = explode($delimiter, $string);
            $newwords = array();
            foreach ($words as $word)
            {
                if (in_array(strtoupper($word), $uppercase_exceptions))
                    $word = strtoupper($word);
                else
                    if (!in_array($word, $lowercase_exceptions))
                        $word = ucfirst($word);

                $newwords[] = $word;
            }

            if (in_array(strtolower($delimiter), $lowercase_exceptions))
                $delimiter = strtolower($delimiter);

            $string = join($delimiter, $newwords);
        }
        return $string;
    }

} 