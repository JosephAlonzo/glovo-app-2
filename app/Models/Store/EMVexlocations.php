<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class EMVexlocations extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table          = 'vexsol_store_locations';
    protected $primaryKey     = 'STLO_ID';
    public    $timestamps     = false;

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
     * -----------------------------------------------------------------------------------------------------------------
     * METHOD ATTRIBUTES
     * -----------------------------------------------------------------------------------------------------------------
     */
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getAttribute($this->primaryKey);
    }


    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setAttribute($this->primaryKey, $id);
        return $this;
    }



    /**
     * @param $setting
     * @return $this
     */
    public function setSetting($setting)
    {
        $this->setAttribute('STLO_SETTING', $setting);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSetting()
    {
        return $this->getAttribute('STLO_SETTING');
    }


    /**
     * @param $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->setAttribute('STLO_STOREID', $store);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStore()
    {
        return $this->getAttribute('STLO_STOREID');
    }



    /**
     * @param $lat
     * @return $this
     */
    public function setLat($lat)
    {
        $this->setAttribute('STLO_LAT', $lat);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->getAttribute('STLO_LAT');
    }



    /**
     * @param $lng
     * @return $this
     */
    public function setLng($lng)
    {
        $this->setAttribute('STLO_LNG', $lng);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLng()
    {
        return $this->getAttribute('STLO_LNG');
    }


    /**
     * @param $lng
     * @return $this
     */
    public function setName($lng)
    {
        $this->setAttribute('STLO_NAME', $lng);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->getAttribute('STLO_NAME');
    }



    /**
     * @param $lng
     * @return $this
     */
    public function setCity($lng)
    {
        $this->setAttribute('STLO_CITY', $lng);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->getAttribute('STLO_CITY');
    }


    /**
     * @param $address
     * @return $this
     */
    public function setAddress1($address)
    {
        $this->setAttribute('STLO_ADDRESS1', $address);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress1()
    {
        return $this->getAttribute('STLO_ADDRESS1');
    }


    /**
     * @param $address
     * @return $this
     */
    public function setAddress2($address)
    {
        $this->setAttribute('STLO_ADDRESS2', $address);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress2()
    {
        return $this->getAttribute('STLO_ADDRESS2');
    }


    /**
     * @param $address
     * @return $this
     */
    public function setPostCode($address)
    {
        $this->setAttribute('STLO_POSTCODE', $address);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostCode()
    {
        return $this->getAttribute('STLO_POSTCODE');
    }


    /**
     * @param $address
     * @return $this
     */
    public function setCountry($address)
    {
        $this->setAttribute('STLO_COUNTRY', $address);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->getAttribute('STLO_COUNTRY');
    }



    /**
     * @param $address
     * @return $this
     */
    public function setCountryName($address)
    {
        $this->setAttribute('STLO_COUNTRY', $address);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountryName()
    {
        return $this->getAttribute('STLO_COUNTRY_NAME');
    }






    /**
     * @param $address
     * @return $this
     */
    public function setProvince($province)
    {
        $this->setAttribute('STLO_PROVINCE', $province);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProvince()
    {
        return $this->getAttribute('STLO_PROVINCE');
    }



    /**
     * @param $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->setAttribute('STLO_PHONE', $phone);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->getAttribute('STLO_PHONE');
    }


    /**
     * @param $enable
     * @return $this
     */
    public function setEnable($enable)
    {
        $this->setAttribute('STLO_ENABLE', $enable);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnable()
    {
        return $this->getAttribute('STLO_ENABLE');
    }

































}
