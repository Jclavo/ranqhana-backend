<?php

namespace Tests\Unit;

use App\Invoice;
use App\InvoiceDetail;
use App\Actions\Invoice\InvoiceAnull;
use Tests\TestCase;

class ActionInvoiceAnullTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->setBaseModel('App\Invoice');
        $this->setFieldsDatabaseHas(['id', 'subtotal', 'taxes', 'discount', 'total', 'user_id', 'type_id', 'store_id', 'stage']);  
    }

    public function test_action_anull_invoice()
    {
        // $item = factory(Item::class)->create();

        $invoice = factory(Invoice::class,'full')->create();
        $invoiceDetails = factory(InvoiceDetail::class,'full',3)->create(['invoice_id' => $invoice->id]);

        $invoiceAnull = new InvoiceAnull($invoice);

        $invoiceAnull->execute();

        //Change the stage to be ANULLED
        $invoice->setStageAnulled();

        // $this->assertDatabaseHas('invoices', $invoice->toArray());
        $this->setResultResponseSimple($invoice->toArray());
        $this->checkRecordInDB();
    }
}
