<?php

namespace App\Actions\User;

use App\User;

class UserIsFree
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    } 

    public function passes()
    {
        $count = $this->user::
                join('invoices', 'users.id', '=', 'invoices.user_id')
                ->where('users.id', '=', $this->user->id)
                ->count();

        if($count > 0) return false;
        
        return true;
    }

    public function message()
    {
        return 'Store can not be modify, because the user has already started to work.';
    }
}