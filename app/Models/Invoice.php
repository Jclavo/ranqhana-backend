<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class Invoice extends BaseModel 
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'serie', 'subtotal', 'taxes', 'discount', 'total', 'type_id', 'stage_id', 'user_id'
    ];

    /**
     * The attributes that are cast.
     */
    protected $casts = [
        // 'subtotal' => 'decimal:2',
        //  'discount' => 'decimal:2',
    ];


    //Relationships

    /**
     * Get the "Type" associated with the "Invoice".
     */
    public function type()
    {
        return $this->belongsTo('App\Models\InvoiceTypes');
    }

    /**
     * Get the "Stage" associated with the "Invoice".
     */
    public function stage()
    {
        return $this->belongsTo('App\Models\InvoiceStages');
    }

    /**
     * Get the "Invoice details" for the "Invoice"
    */
    public function details()
    {
        return $this->hasMany('App\Models\InvoiceDetail');
    }



    //Custom functions
    public function calculateTotal(){
        return (($this->subtotal + $this->taxes) - $this->discount);
    }

    //Getter (statics)

    static function getStagePaid()
    {
        return 1;
    }

    static function getStageAnulled()
    {
        return 2;
    }

    static function getTypeForSell(){
        return 1;
    }

    static function getTypeForPurchase(){
        return 2;
    }

    
    //Getter 
    public function getType(){
        return $this->type_id;
    }


    //Setter and Getters
    public function setStagePaid() // stage = 'P';
    {
        $this->stage_id = 1;
    }

    public function setStageAnulled() // stage = 'A';
    {
        $this->stage_id = 2;
    }


 
}
