<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Models\Item;

use Illuminate\Support\Facades\DB;
use App\Actions\Item\ItemHasStock;

//Services
use App\Services\LanguageService;

class InvoiceAnull
{
    protected $invoice;
    protected $failMessage;
    protected $languageService;

    public function __construct($invoice)
    {
        //initialize language service
        $this->languageService = new LanguageService();
    
        //set localization
        $this->failMessage = $this->languageService->getSystemMessage('invoice.already-anulled');

        // $this->invoice = Invoice::findOrFail($invoice_id); 
        $this->invoice = $invoice; 
    }
    
    public function execute()
    {
        if($this->invoice->stage_id == $this->invoice->getStageAnulled()) return false; 

        $invoice_id = $this->invoice->id;
        $results = Invoice::
                select('invoices.id', 'invoice_details.item_id', 'invoice_details.quantity' )
                ->join('invoice_details', function ($join) use($invoice_id){
                    $join->on('invoice_details.invoice_id', '=', 'invoices.id');
                       
                })
                ->where('invoices.id', '=', $invoice_id)
                ->get();

        $invoiceType = $this->invoice->getType();

        //beginTransaction
        DB::beginTransaction();

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
                    $this->failMessage = $this->languageService->getSystemMessage('invoice.has-no-stock');
                    DB::rollBack();
                    return false;
                }
            }
            $item->save();
        }
        
        $this->invoice->setStageAnulled();
        $this->invoice->save();
        
        //end transaction
        DB::commit();
        return true;
    }

    public function message()
    {
        // return 'Invoice has already been anulled';
        return $this->failMessage;
    }
}