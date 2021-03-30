<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EMVexlanguages extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table          = 'vexsol_langs';
    protected $primaryKey     = 'LANG_CODE';
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
}
