<?php


namespace App\Scopes;

use App\Facades\Context;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class ObraScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('id_obra', '=', Context::getIdObra());
    }
}
