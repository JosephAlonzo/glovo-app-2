<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EMVexstore extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table          = 'vexsol_store_general';
    protected $primaryKey     = 'STORE_ID';
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
        $this->setAttribute('STORE_ID', $id);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getAttribute('STORE_ID');
    }



    /**
     * @param $id
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->setAttribute('STORE_DOMAIN', $domain);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->getAttribute('STORE_DOMAIN');
    }



    /**
     * @param $id
     * @return $this
     */
    public function setTimeZone($id)
    {
        $this->setAttribute('STORE_IANA_TIMEZONE', $id);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeZone()
    {
        return $this->getAttribute('STORE_IANA_TIMEZONE');
    }

    /**
     * @param $id
     * @return $this
     */
    public function setNumProducts($id)
    {
        $this->setAttribute('STORE_NUM_PRODUCTS', $id);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumProducts()
    {
        return $this->getAttribute('STORE_NUM_PRODUCTS');
    }

    /**
     * @param $id
     * @return $this
     */
    public function setCarrierId($carrier)
    {
        $this->setAttribute('STORE_CARRIER_ID', $carrier);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCarrierId()
    {
        return $this->getAttribute('STORE_CARRIER_ID');
    }


    /**
     * @param $id
     * @return $this
     */
    public function setEmail($email)
    {
        $this->setAttribute('STORE_EMAIL', $email);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->getAttribute('STORE_EMAIL');
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
    public static function  findByStoreId($id){
        return self::where('STORE_ID', $id)->first();
    }


    /**
     * Find the config user
     * @param $id
     * @return self
     */
    public static function  findByDomain($domain){
        return self::where('STORE_DOMAIN', $domain)->first();
    }



}
