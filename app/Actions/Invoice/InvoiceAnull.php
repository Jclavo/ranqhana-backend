<?php

namespace App\Actions\Invoice;

use App\Invoice;
use App\Item;

use App\Actions\Item\ItemHasStock;

class InvoiceAnull
{
    protected $invoice;
    protected $failMessage = 'Invoice has already been anulled';

    public function __construct($invoice)
    {
        // $this->invoice = Invoice::findOrFail($invoice_id); 
        $this->invoice = $invoice; 
    }
    
    public function execute()
    {
        if($this->invoice->stage == 'A') return false; 

        $invoice_id = $this->invoice->id;
        $results = Invoice::
                select('invoices.id', 'invoice_details.item_id', 'invoice_details.quantity' )
                ->join('invoice_details', function ($join) use($invoice_id){
                    $join->on('invoice_details.invoice_id', '=', 'invoices.id');
                       
                })
                ->where('invoices.id', '=', $invoice_id)
                ->get();

        $invoiceType = $this->invoice->getType();

        foreach ($results as $result) {
            $item = Item::findOrFail($result->item_id);

            if($invoiceType == $this->invoice->getTypeForSell()){
                $item->increaseStock($result->quantity);
            }else if($invoiceType == $this->invoice->getTypeForPurchase()){
                
                //Validate if it has stock is missing

                // $checkStock = new ItemHasStock($item, $result->quantity);

                if($item->isStocked()){
                    $item->decreaseStock($result->quantity);
                }
                else{
                    $this->failMessage = 'There is not enough stock available.';
                    return false;
                }
            }
            $item->save();
        }
        
        $this->invoice->setStageAnulled();
        $this->invoice->save();
        
        return true;
    }

    public function message()
    {
        // return 'Invoice has already been anulled';
        return $this->failMessage;
    }
}