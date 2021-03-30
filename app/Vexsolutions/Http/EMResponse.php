<?php


namespace App\Library\Http;



use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


class EMResponse {

    /**
     * @var array
     * $response = array(
     *  'success' =>
     *  'request' =>
     * )
     */
    public $response  = array();
    public $robject   = array();




    public function __construct(){

        $this->response = array();

    }



    public function get($key=null, $back=false){

        if($key){
            if(key_exists($key,$this->response)){
                return $this->response[$key];
            }

            return $back;
        }
        return $this->response;
    }
    public function set($object){
        $this->response = $object;
        return $this;
    }



    public function built(){
        $this->token();
        $this->response = array(
            'success' => true,
            'request' => $this->response
        );

        return $this;
    }


    public function fail( $errors=[] ){
        $this->setTransaction(false);
        $this->response['errors']      = $errors;

        return $this;

    }

    public function getError($key){
        $errors = $this->response['errors'];
        if(array_key_exists($key,$errors)){
            return $errors[$key];
        }

        return null;

    }

    public function success($data=[], $autoincrement=false){

        if($autoincrement){
          $this->response['autoincrement'] = $autoincrement;
        }

        $this->setTransaction(true);
        $this->response['data'] = $data;

        return $this;
    }

    public function token(){
        $this->response['session'] = array ( 'connected'=> Auth::check() , 'token'=>Session::getToken()) ;
        return $this;
    }


    public function setTransaction($trans){
        $this->response['transaction'] = $trans;
        return $this;
    }

    public function reset (){

        $this->response = array();
    }

    public function json(){
        return Response::json($this->response);

    }


} 