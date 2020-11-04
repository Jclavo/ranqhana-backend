<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class Belongs2CompanyByInvoice implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // $builder->join('invoices', 'invoice_id', '=', 'invoices.id')    
        //         ->whereIn('invoices.user_id', function($query){
        //             $query->select('ranqhana_users.id')
        //                 ->from('ranqhana_users')
        //                 ->where('ranqhana_users.company_project_id', '=', Auth::user()->company_project_id);
        // });
        $builder->whereIn('invoice_id', function($query){
                    $query->select('invoices.id')
                          ->join('ranqhana_users', 'user_id', '=', 'ranqhana_users.external_user_id')
                          ->from('invoices')
                          ->where('ranqhana_users.company_project_id', '=', Auth::user()->company_project_id);
        });
    }
}