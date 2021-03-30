<?php
/**
 * Created by PhpStorm.
 * User: lescoffie
 * Date: 2019-05-17
 * Time: 06:25
 */

class vexsolucionesglovoOrder
{

    public $glovo_order_id;
    public $description;
    public $schedule_time;
    public $address_origin_lat;
    public $address_origin_lng;
    public $address_origin_label;
    public $address_origin_details;
    public $address_destination_lat;
    public $address_destination_lng;
    public $address_destination_label;
    public $address_destination_details;
    public $address_destination_phone;
    public $address_destination_person;
    public $state;
    public $status;
    public $message;
    public $created_at;


    public function __construct($order_id=null)
    {

    }


    public function getOrderGlovo($order_id){
        global $db;

        $sql = "SELECT * FROM ".TABLE_VEXSOL_GLOVO_ORDERS." WHERE order_id = (':order_id');";
        $sql = $db->bindVars($sql, ':order_id', $order_id, 'integer');
        $glovoorder = $db->Execute($sql);

        if ( $glovoorder->RecordCount() == 0){
            return false;
        }


        $this->order_id                     = $glovoorder->fields['order_id'];
        $this->glovo_order_id               = $glovoorder->fields['glovo_order_id'];
        $this->description                  = $glovoorder->fields['description'];
        $this->schedule_time                = $glovoorder->fields['schedule_time'];
        $this->address_origin_lat           = $glovoorder->fields['address_origin_lat'];
        $this->address_origin_lng           = $glovoorder->fields['address_origin_lng'];
        $this->address_origin_label         = $glovoorder->fields['address_origin_label'];
        $this->address_origin_details       = $glovoorder->fields['address_origin_details'];
        $this->address_origin_phone         = $glovoorder->fields['address_origin_phone'];

        $this->address_destination_lat      = $glovoorder->fields['address_destination_lat'];
        $this->address_destination_lng      = $glovoorder->fields['address_destination_lng'];
        $this->address_destination_label    = $glovoorder->fields['address_destination_label'];
        $this->address_destination_details  = $glovoorder->fields['address_destination_details'];
        $this->address_destination_phone    = $glovoorder->fields['address_destination_phone'];
        $this->address_destination_person   = $glovoorder->fields['address_destination_person'];

        $this->state                        = $glovoorder->fields['state'];
        $this->status                       = $glovoorder->fields['status'];
        $this->message                      = $glovoorder->fields['message'];
        $this->created_at                   = $glovoorder->fields['created_at'];

        return $this;
    }


    /**
     * @param $order_id
     * @param $state
     * @return $this
     */
    public function updateState($order_id, $state){
        global $db;

        $sql = "UPDATE " .TABLE_VEXSOL_GLOVO_ORDERS . " SET state = :state WHERE order_id = :order_id;";
        $sql = $db->bindVars($sql, ':state', $state, 'string');
        $sql = $db->bindVars($sql, ':order_id', $order_id, 'integer');
        $db->Execute($sql);

        return $this;

    }



}