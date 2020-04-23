<?php

namespace App\Policies;

use App\User;
use App\InvoiceDetail;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoiceDetailPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function isMine(User $user, $invoiceDetail, $item){
        // return $user->store_id == $invoiceDetail->store_id;
        // ? Response::allow(): Response::deny('You do not own this post.'); 
        return true;
    }
}
