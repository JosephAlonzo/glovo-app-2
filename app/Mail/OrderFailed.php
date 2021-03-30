<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderFailed extends Mailable
{
    use Queueable, SerializesModels;

    public $shopifyorderid;
    public $shopifyshop;
    public $shopifyorder;
    public $errors;
    public $emglovoorder;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($shopifyorderid, $shopifyshop, $shopifyorder, $emglovoorder, $errors)
    {

        $this->shopifyorderid   = $shopifyorderid;
        $this->shopifyshop      = $shopifyshop;
        $this->shopifyorder     = $shopifyorder;
        $this->errors           = $errors;
        $this->emglovoorder     = $emglovoorder;
    }



    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('glovodeliveryshopify@gmail.com', $this->shopifyshop->name)
            ->bcc('leshcoff@gmail.com')
            ->subject(__('glovo.mailfailed.subject'))
            ->view('shopify.mail.orders.failed');
    }
}
