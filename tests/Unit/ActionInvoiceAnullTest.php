<?php

namespace Tests\Unit;

use App\Invoice;
use App\InvoiceDetail;
use App\Item;
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

    public function test_action_anull_sell_invoice_ok()
    {
        // $item = factory(Item::class)->create();

        $invoice = factory(Invoice::class,'full')->create(['type_id' => 1]);
        $invoiceDetails = factory(InvoiceDetail::class,'full',3)->create(['invoice_id' => $invoice->id]);

        $invoiceAnull = new InvoiceAnull($invoice);

        $invoiceAnull->execute();

        //Change the stage to be ANULLED
        $invoice->setStageAnulled();

        // $this->assertDatabaseHas('invoices', $invoice->toArray());
        $this->setResultResponseSimple($invoice->toArray());
        $this->checkRecordInDB();
    }

    public function test_action_anull_purchase_invoice_ok()
    {
        // $item = factory(Item::class)->create();

        $invoice = factory(Invoice::class,'full')->create(['type_id' => 2]);
        $item = factory(Item::class)->create(['stocked' => true ]);
        $invoiceDetails = factory(InvoiceDetail::class,'full',3)->create(['invoice_id' => $invoice->id, 'item_id' => $item->id]);

        $invoiceAnull = new InvoiceAnull($invoice);

        $this->assertTrue($invoiceAnull->execute());

        //Change the stage to be ANULLED
        $invoice->setStageAnulled();

        // $this->assertDatabaseHas('invoices', $invoice->toArray());
        $this->setResultResponseSimple($invoice->toArray());
        $this->checkRecordInDB();
    }

    public function test_action_anull_purchase_invoice_fail()
    {
        // $item = factory(Item::class)->create();

        $invoice = factory(Invoice::class,'full')->create(['type_id' => 2]);

        $item = factory(Item::class)->create(['stocked' => false ]);
        $invoiceDetails = factory(InvoiceDetail::class,'full', 3)->create(['invoice_id' => $invoice->id, 'item_id' => $item->id]);
        
        $invoiceAnull = new InvoiceAnull($invoice);

        $this->assertFalse($invoiceAnull->execute());

        $this->assertEquals('There is not enough stock available.', $invoiceAnull->message());

        $this->setResultResponseSimple($invoice->toArray());
        $this->checkRecordInDB();
    }
}
