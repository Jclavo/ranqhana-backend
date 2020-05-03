<?php

namespace Tests\Unit;

use App\Invoice;
use App\Utils\InvoiceUtils;

use Tests\TestCase;

class UtilsInvoiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // public function test_util_invoice_get_type()
    // {
    //     $invoice = factory(Invoice::class)->create();

    //     $type = InvoiceUtils::getType($invoice);

    //     $this->assertIsInt($type);
    // }
}
