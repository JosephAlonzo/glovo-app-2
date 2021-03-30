<?php

namespace Escom\Model;
use Illuminate\Database\Eloquent\Model;


class EMBase extends Model{


    var $errors = array();

    public function __construct($attributes=[])
    {
        parent::__construct($attributes);
    }



    /**
     * Set up event listeners for all Item types.
     * Named events are mapped to trait methods in $events.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function($model)
        {
            //dd($model);
        });

        static::updated(function($model)
        {
            //dd($model);
        });


    }


    /**
     * This makes each and every column is fillable from that table
     */
    public function setFillable()
    {
        $fields = \Schema::getColumnListing( $this->getTable() );
        $this->fillable[] = $fields;
    }


    /**
     * Build the sql whith bindings params
     * @return mixed
     */
    public function getSql()
    {
        $builder = $this->getBuilder();
        $sql = $builder->toSql();
        foreach($builder->getBindings() as $binding)
        {
            $value = is_numeric($binding) ? $binding : "'".$binding."'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }
        return $sql;
    }


    /**
     * Reload attributes of model instance
     *
     * @return void
     */
    public function reload()
    {
        if (!$this->exists) {
            return;
        }
        // Get new instance
        $model = static::find($this->getKey());

        // Reload attributes
        $this->attributes = $model->getAttributes();
        $this->syncOriginal();
    }


    /**
     * Returns an associative array of object properties
     *
     * @access	public
     * @param	string  $error, name of de error cause the exception ex. error = array(code, message)
     * @param	integer $code , code of de error
     * @param	string  $msg  , description of the message
     * @return	array[code] = message
     */
    public function setError($error, $code, $msg){
        $this->errors[$error] = array('code'=>$code, 'message'=>$msg);
    }


    /**
     * Returns an associative array of object properties
     *
     * @access	public
     * @param	string  $error, if empty return all array with errors, else return property
     * @return	array[code] = message
     */
    public function getError($error=null){
        if ($error) {
            if ( ! array_key_exists($error, $this->errors) ) {
                return false;
            }
            else {

                return $this->errors[$error];
            }
        }

        return $this->errors;
    }

    /**
     * Reset object errors
     */
    public function resetError(){
        $this->errors = array();
    }


    /**
     * @param Exception $e
     * @return array
     */
    public function WhatHappen(Exception $e){
        $error = $this->getError('error');
        $errores = array();

        if($error){
            $errors = array(
                'message'    => $error['message'],
                'code'       => $error['code'],
                'trace'      => isset($error['trace'])?$error['trace'] : "SIN MENSAJE DESCRIPCIÓN"
            );
        }else{
            $errors = array(
                'message'    => $e->getMessage(),
                'code'       => $e->getCode(),
                'line'       => $e->getLine()
            );

        }

        return $errors;

    }


    /**
     * @param int $len
     * @return string
     */
    public function string($length=30){

        if ( ! function_exists('openssl_random_pseudo_bytes'))
        {
            throw new RuntimeException('OpenSSL extension is required.');
        }

        $bytes = openssl_random_pseudo_bytes($length * 2);

        if ($bytes === false)
        {
            throw new RuntimeException('Unable to generate random string.');
        }

        $string =  substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);

        return $string;
    }




} 