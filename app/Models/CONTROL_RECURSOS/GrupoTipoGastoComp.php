<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class GrupoTipoGastoComp extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'grupostipogastocomp';
    protected $primaryKey = 'IdGrupoTipoGastoComp';
}
