<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

class EMVexhollyday extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table          = 'vexsol_store_hollydays';
    protected $primaryKey     = 'HODAY_HOLLYDAY';
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
     * @return mixed
     */
    public function getSetting()
    {
        return $this->getAttribute('HODAY_SETTING');
    }

    /**
     * @param $id
     * @return $this
     */
    public function setSetting($id)
    {
        $this->setAttribute('HODAY_SETTING', $id);
        return $this;
    }





    /**
     * @param $setting
     */
    public function getHollyDays($setting=""){

        $hollydays = [];

        #get non working days
        $settinghollydays = $this->where('HODAY_SETTING', $this->getSetting())->get();

        foreach ($settinghollydays as $day)
        {
            $hollydays[] = [
                'HODAY_HOLLYDAY'    => $day->HODAY_HOLLYDAY,
                'HODAY_SETTING'     => $day->HODAY_SETTING,
                'HODAY_DAY'         => $day->HODAY_DAY,
                'HODAY_MONTH'       => $day->HODAY_MONTH,
                'HODAY_CREATED_AT'  => $day->HODAY_CREATED_AT,
                'DATE'              => "{year}-". $day->HODAY_MONTH . "-".$day->HODAY_DAY,
                'MDAY'              => $day->HODAY_MONTH.$day->HODAY_DAY,
            ];
        }

        return $hollydays;

    }








}
