<?php

namespace App\Actions\Invoice;

use Carbon\Carbon;

class InvoiceIsOpened
{
    protected $invoice;
    protected $currentDate;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
        $this->currentDate = Carbon::now();
    }    
    
    public function passes()
    {
        $this->currentDate->subMinute();

        if($this->currentDate->greaterThan($this->invoice->created_at)) return false;
        
        return true;
    }    
    
    public function message()
    {
        return 'Invoice is out the date range.';
    }
}