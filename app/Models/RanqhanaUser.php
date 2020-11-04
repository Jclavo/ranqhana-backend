<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RanqhanaUser extends Model
{
    protected $fillable = [
        'external_user_id', 'login', 'company_project_id'
    ];

    /**
     * Get the roles for the project
    */
    public function items()
    {
        return $this->hasMany('App\Models\Item')
                    ->orderBy('name');
    }
}
