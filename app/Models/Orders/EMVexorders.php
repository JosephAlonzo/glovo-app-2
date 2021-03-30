<?php

namespace App\Models\Orders;

use App\Models\EMVexglovoorders;
use Illuminate\Database\Eloquent\Model;

class EMVexorders extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table          = 'vexsol_store_orders';
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


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * METODOS PARA EL MANEJO DE LOS ATRIBUTOS
     * -----------------------------------------------------------------------------------------------------------------
     */


    /**
     * Find the config user
     * @param $id
     * @return \App\Models\Orders\EMVexorders
     */
    public static function  findByOrderId($id){

        return self::where('id', $id)->first();
    }


}
