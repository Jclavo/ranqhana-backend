<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Item;

use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
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

    public function isMyItem(User $user, Item $item){
        return $user->store_id == $item->store_id;
    }
}
