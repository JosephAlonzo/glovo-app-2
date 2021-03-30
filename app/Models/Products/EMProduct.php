<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class EMProduct extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table          = 'products';
    protected $primaryKey     = 'PROD_PRODUCT';
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
}
