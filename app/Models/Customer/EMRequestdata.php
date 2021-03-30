<?php

namespace App\Models\Customer;

use App\Models\Orders\EMVexorders;
use Illuminate\Database\Eloquent\Model;

class EMRequestdata extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table          = 'request_redact';
    protected $primaryKey     = 'id';
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
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setAttribute('id', $id);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }




}
