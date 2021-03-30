<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderTracking extends Mailable
{
    use Queueable, SerializesModels;

    public $emglovoorder;
    public $shopifyshop;
    public $shopifyorder;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($shopifyshop, $shopifyorder, $emglovoorder, $data)
    {
        $this->shopifyshop  = $shopifyshop;
        $this->shopifyorder = $shopifyorder;
        $this->emglovoorder = $emglovoorder;
        $this->data         = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this
            ->from('glovodeliveryshopify@gmail.com', $this->shopifyshop->name)
            ->bcc('leshcoff@gmail.com')
            ->subject('Glovo Delivery - '.__('glovo.mailtracking.subject') .' '.$this->shopifyorder->name)
            ->view('shopify.mail.tracking.track');

    }
}
