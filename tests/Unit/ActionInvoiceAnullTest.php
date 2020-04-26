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
    }

    public function test_invoice_anull_testing()
    {
        // $item = factory(Item::class)->create();

        $invoice = factory(Invoice::class,'full')->create();
        $invoiceDetails = factory(InvoiceDetail::class,'full',3)->create(['invoice_id' => $invoice->id]);

        $invoiceAnull = new InvoiceAnull($invoice);

        $invoiceAnull->execute();

        //Change the stage to be ANULLED
        $invoice->setStageAnulled();

        $this->assertDatabaseHas('invoices', $invoice->toArray());
    }
}
