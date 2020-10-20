<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class InvoiceDetail extends BaseModel
{
    protected $fillable = [
        'quantity', 'price', 'total', 'item_id', 'invoice_id'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('belongs2Company', function (Builder $builder) {
            $builder->join('invoices', 'invoice_id', '=', 'invoices.id')    
                    ->whereIn('invoices.user_id', function($query){
                        $query->select('ranqhana_users.id')
                            ->from('ranqhana_users')
                            ->where('ranqhana_users.company_project_id', '=', Auth::user()->company_project_id);
                    });

        });
    }

    //Relationships
    
    /**
     * Get the "Invoice" associated with the "Invoice Detail".
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice');
    }

    /**
     * Get the "Item" associated with the "Invoice Detail".
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Item');
    }


    //Custom functions
    public function calculateTotal()
    {
        $this->total = $this->quantity * $this->price;
    }


}
