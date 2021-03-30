<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class onServiceTest extends Mailable
{
    use Queueable, SerializesModels;

    public $response;
    public $shop;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($shop, $response )
    {
        $this->response     = $response;
        $this->shop         = $shop;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('shopify.mail.setting.testing')
            ->from('leshcoff@gmail.com', 'Shopify - Glovo Delivery Notification')
            ->subject('Testing coverage address shop - '. $this->shop->shopify_domain)
            ->with('response', $this->response)
            ->with('shop', $this->shop);
    }
}
