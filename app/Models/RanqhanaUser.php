<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RanqhanaUser extends Model
{
    protected $fillable = [
        'external_user_id', 'company_project_id'
    ];
}
