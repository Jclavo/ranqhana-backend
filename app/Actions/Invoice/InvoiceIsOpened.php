<?php

namespace App\Actions\Invoice;

use Carbon\Carbon;

//Services
use App\Services\LanguageService;

class InvoiceIsOpened
{
    protected $invoice;
    protected $currentDate;
    protected $languageService;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
        $this->currentDate = Carbon::now();

        //initialize language service
	    $this->languageService = new LanguageService();
    }    
    
    public function passes()
    {
        $this->currentDate->subMinute();

        if($this->currentDate->greaterThan($this->invoice->created_at)) return false;
        
        return true;
    }    
    
    public function message()
    {
        return $this->languageService->getSystemMessage('invoice.out-date');
    }
}