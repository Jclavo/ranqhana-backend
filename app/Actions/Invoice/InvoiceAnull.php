<?php

namespace App\Actions\Invoice;

use App\Invoice;
use App\Item;

class InvoiceAnull
{
    protected $invoice;

    public function __construct($invoice)
    {
        // $this->invoice = Invoice::findOrFail($invoice_id); 
        $this->invoice = $invoice; 
    }
    
    public function execute()
    {
        $invoice_id = $this->invoice->id;
        $results = Invoice::
                select('invoices.id', 'invoice_details.item_id', 'invoice_details.quantity' )
                ->join('invoice_details', function ($join) use($invoice_id){
                    $join->on('invoice_details.invoice_id', '=', 'invoices.id');
                       
                })
                ->where('invoices.id', '=', $invoice_id)
                ->get();

        foreach ($results as $result) {
            $item = Item::findOrFail($result->item_id); 
            $item->increaseStock($result->quantity);
            $item->save();
        }
        
        $this->invoice->setStageAnulled();
        $this->invoice->save();
        
        return true;
    }
}