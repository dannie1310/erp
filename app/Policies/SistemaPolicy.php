<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;
use App\Permissions\SistemaPermissions;

class SistemaPolicy
{
    use HandlesAuthorization;

    //todo: add extra checks. For example: user->id === $requestedItem->owner->id

    public function index(User $user)
    {
        return $user->tokenCan(SistemaPermissions::RETRIEVE_ALL_SISTEMAS);
    }

    public function show(User $user, $requestedItem)
    {
        return $user->tokenCan(SistemaPermissions::RETRIEVE_SISTEMA);
    }

    public function create(User $user)
    {
        return $user->tokenCan(SistemaPermissions::CREATE_SISTEMA);
    }

    public function update(User $user, $requestedItem)
    {
        return $user->tokenCan(SistemaPermissions::UPDATE_SISTEMA);
    }

    public function delete(User $user, $requestedItem)
    {
        return $user->tokenCan(SistemaPermissions::DELETE_SISTEMA);
    }
}
