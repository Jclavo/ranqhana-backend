<?php

namespace App\Actions\Invoice;

//Services
use App\Services\LanguageService;

class InvoiceHasEnoughSubtotal
{
    protected $invoice;
    protected $invoiceDetail;
    protected $languageService;

    public function __construct($invoice, $invoiceDetail)
    {
        $this->invoice = $invoice;
        $this->invoiceDetail = $invoiceDetail;

        //initialize language service
	    $this->languageService = new LanguageService();
    }    
    
    public function passes()
    {
        if($this->invoiceDetail->total > $this->invoice->subtotal) return false;
        
        return true;
    }    
    
    public function message()
    {
        return $this->languageService->getSystemMessage('invoice.detail-gt-subtotal');
    }
}