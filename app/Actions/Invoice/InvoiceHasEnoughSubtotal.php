<?php

namespace App\Actions\Invoice;

class InvoiceHasEnoughSubtotal
{
    protected $invoice;
    protected $invoiceDetail;

    public function __construct($invoice, $invoiceDetail)
    {
        $this->invoice = $invoice;
        $this->invoiceDetail = $invoiceDetail;
    }    
    
    public function passes()
    {
        if($this->invoiceDetail->total > $this->invoice->subtotal) return false;
        
        return true;
    }    
    
    public function message()
    {
        return __('messages.invoice.detail-gt-subtotal');
    }
}