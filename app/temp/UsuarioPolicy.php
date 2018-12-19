<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;
use App\Permissions\UsuarioPermissions;

class UsuarioPolicy
{
    use HandlesAuthorization;

    //todo: add extra checks. For example: user->id === $requestedItem->owner->id

    public function index(User $user)
    {
        return $user->tokenCan(UsuarioPermissions::RETRIEVE_ALL_USUARIOS);
    }

    public function show(User $user, $requestedItem)
    {
        return $user->tokenCan(UsuarioPermissions::RETRIEVE_USUARIO);
    }

    public function create(User $user)
    {
        return $user->tokenCan(UsuarioPermissions::CREATE_USUARIO);
    }

    public function update(User $user, $requestedItem)
    {
        return $user->tokenCan(UsuarioPermissions::UPDATE_USUARIO);
    }

    public function delete(User $user, $requestedItem)
    {
        return $user->tokenCan(UsuarioPermissions::DELETE_USUARIO);
    }
}
