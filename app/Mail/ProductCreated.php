<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $store;

    /**
     * Create a new message instance.
     *
     * @param Product $product
     * @param Store $store
     */
    public function __construct(Product $product , Store $store)
    {
        $this->product = $product;
        $this->store = $store;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.product_created');
    }
}
