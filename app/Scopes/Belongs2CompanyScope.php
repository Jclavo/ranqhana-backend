<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class Belongs2CompanyScope implements Scope
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
        //$builder->where('age', '>', 200);
        $builder->whereIn('user_id', function($query){
            $query->select('ranqhana_users.external_user_id')
                ->from('ranqhana_users')
                ->where('ranqhana_users.company_project_id', '=', Auth::user()->company_project_id);
        });
    }
}