<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UnitPolicy
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

    /**
     * 
     */
    public function isMyUnit(User $user, Unit $unit){
        return $user->store_id == $unit->store_id;
        // ? Response::allow(): Response::deny('You do not own this post.'); 
    }

}
