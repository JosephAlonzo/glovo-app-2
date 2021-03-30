<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EMVexglovoorders extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table          = 'vexsol_orders_glovo';
    protected $primaryKey     = 'ORGL_ORDER_ID';
    public    $timestamps     = true;

    /**,
     * define which attributes are mass assignable (for security)
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded          = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates            = [];






    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setAttribute('ORGL_UID', $id);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getAttribute('ORGL_UID');
    }


    /**
     * @param $id
     * @return $this
     */
    public function setOrderId($id)
    {
        $this->setAttribute('ORGL_ORDER_ID', $id);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->getAttribute('ORGL_ORDER_ID');
    }


    /**
     * @param $id
     * @return $this
     */
    public function setGlovoOrderId($id)
    {
        $this->setAttribute('ORGL_GLOVO_ID', $id);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGlovoOrderId()
    {
        return $this->getAttribute('ORGL_GLOVO_ID');
    }





    /**
     * @param $id
     * @return $this
     */
    public function setState($state)
    {
        $this->setAttribute('ORGL_STATE', $state);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->getAttribute('ORGL_STATE');
    }




    /**
     * @param $tz
     * @return $this
     */
    public function setScheduledTime($st)
    {
        $this->setAttribute('ORGL_SCHEDULE_TIME', $st);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getScheduledTime()
    {
        return $this->getAttribute('ORGL_SCHEDULE_TIME');
    }



    /**
     * @param $tz
     * @return $this
     */
    public function setTimeZone($tz)
    {
        $this->setAttribute('ORGL_DATETIMEZONE', $tz);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeZone()
    {
        return $this->getAttribute('ORGL_DATETIMEZONE');
    }




    /**
     * @param $id
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setAttribute('ORGL_STATUS', $status);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->getAttribute('ORGL_STATUS');
    }



    /**
     * @param $id
     * @return $this
     */
    public function setErrorMessage($message)
    {
        $this->setAttribute('ORGL_MESSAGE', $message);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->getAttribute('ORGL_MESSAGE');
    }















    /**
     * -----------------------------------------------------------------------------------------------------------------
     * METODOS PARA EL MANEJO DE LOS ATRIBUTOS
     * -----------------------------------------------------------------------------------------------------------------
     */

    /**
     * Find the config user
     * @param $id
     * @return self
     */
    public static function  findByGlovoOrderId($id){

        return self::where('ORGL_GLOVO_ID', $id)->first();
    }


    /**
     * Find the config user
     * @param $id
     * @return self
     */
    public static function  findByOrderId($id){

        return self::where('ORGL_ORDER_ID', $id)->first();
    }










}
