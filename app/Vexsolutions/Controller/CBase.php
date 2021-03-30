<?php
/**
 * Created by PhpStorm.
 * User: lescoffie
 * Date: 23/11/15
 * Time: 10:08
 */

namespace Escom\Base;


use App\Http\Requests;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Library\Http\EMResponse;
use App\Models\DEBUG\EMDebug;
use DateTime;
use DateTimeZone;

use Vexsolutions\Utils\Logger\Facades\BufferLog;


class CBase extends Controller{

    public $MResponse;
    public $monolog;
    public $route;
    protected $model;
    protected $autoCommit   = true;
    protected $errors       = array();


    public $debug;
    public static $ndebug;


    public function __construct($data=[])
    {
        //$this->middleware('auth', ['except' => ['doActivar', 'doRegister', 'verify', 'doActivate']]);
        $this->MResponse = new EMResponse();
    }


    public function setLog($file='log'){

        $logFile = storage_path($file);
        $this->monolog = new Logger('log');
        $this->monolog->pushHandler(new StreamHandler($logFile), Logger::INFO);

        return $this;

    }

    /**
     * Enable or disable the autocommit transaccion
     * @param bool|true $commit
     */
    public function setAutocommit($commit=true){
        $this->autoCommit = $commit;
    }

    /**
     * Returns an associative array of object properties
     *
     * @access	public
     * @param	string  $error , name of de error cause the exception ex. error = array(code, message)
     * @param	integer $code  , code of de error
     * @param	string  $msg   , description of the message
     * @param   string  $trace , descripcion de error que paso
     * @return	array[code] = message
     */
    public function setError($error, $code, $msg, $trace=''){

        $errors = $this->getError($error);
        $object = [];


        if( false == $errors) {
            $errors['code']         = $code;
            $errors['message']      = $msg;
            $errors['trace']        = $trace;
        }


        //si existe el error
        if( is_array($errors)){
            //si existe el indice
            //siempre preservara el primer valor que le pasen, excepto si se usa
            //$this->resetError('error');
            if(isset($errors['code'])){
                if( empty($errors['code']) || is_null($errors['code']) ) $errors['code'] = $code;
            }

            if(isset($errors['message'])){
                if( empty($errors['message']) || is_null($errors['message']) ) $errors['message'] = $msg;
            }

            if(isset($errors['trace'])){
                if( empty($errors['trace']) || is_null($errors['trace']) ) $errors['trace'] = $trace;
            }

        }


        $this->errors[$error] = $errors;
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
     * clear error array
     */
    public function resetError($error = null){

        if ($error) {
            if ( array_key_exists($error, $this->errors) ) {
                 $this->errors[$error] = array();
            }

        }
        $this->errors = array();
    }


    /**
     * @param Exception $e
     * @return array
     */
    public function TraceError( $e){
        $error = $this->getError('error');
        $errores = array();

        if($error){
           $errors = array(
               'message'    => $error['message'],
               'code'       => $error['code'],
               'trace'      => isset($error['trace'])?$error['trace'] : "MENSAJE SIN DESCRIPCIÓN DEL ERROR"
           );
        }else{
            $errors = array(
                'message'    => $e->getMessage(),
                'code'       => $e->getCode(),
                'line'       => $e->getLine(),
                'file'       => $e->getFile()
            );

        }


        return $errors;

    }


    /**
     * Crea un log en el archivo especificado y escribe el log
     * @param string $level
     * @param string $message
     * @param null $data
     */
    public function Log($level = 'info', $message='', $data=[]){

        $loglevel = Logger::DEBUG;

        switch ($level){
            case 'info' :
                $loglevel = Logger::INFO;
                break;
            case 'error' :
                $loglevel = Logger::ERROR;
                break;
            case 'alert' :
                $loglevel = Logger::ALERT;
                break;
            case 'notice' :
                $loglevel = Logger::NOTICE;
                break;
            case 'critical' :
                $loglevel = Logger::CRITICAL;
                break;

            default :
                $loglevel = Logger::DEBUG;
                break;
        }


        $this->monolog->log( $loglevel, $message, $data );
    }




    /**
     * Escribe en el buffer del log el mensaje
     * @param string $level
     * @param string $message
     * @param array $data
     * @param int $index
     * @return $this
     */
    public function Debug( $message='', $index=1, $eol=true){

        BufferLog::Debug( $message, $index, $eol);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDebug(){
        return BufferLog::getDebug();
    }

    /**
     * log - Registra las operaciones que hace
     */
    public function clearDebug(){
        BufferLog::clearDebug();
    }


    /**
     * @param $filename
     * @return bool
     */
    function LogToFile($filename=null)
    {
        $file = $filename ? $filename : $this->logFile;
        BufferLog::LogToFile($file);
    }


    /**
     * Guarda en base de datos el texto del debug
     * @param $object
     * @param string $text
     */
    function LogToDataBase($object, $text="")
    {
        BufferLog::LogToDataBase($object,$text);
    }



    /**
     * @param $object
     * @return array
     * @throws \ReflectionException
     */
    public static function toArray($object) {
        $reflectionClass = new \ReflectionClass($object);

        $properties = $reflectionClass->getProperties();

        $array = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            if (is_object($value)) {
                $array[$property->getName()] = self::toArray($value);
            } else {
                $array[$property->getName()] = $value;
            }
        }
        return $array;
    }



}
