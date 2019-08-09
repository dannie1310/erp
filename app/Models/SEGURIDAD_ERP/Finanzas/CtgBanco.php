<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 05:08 PM
 */

namespace App\Models\SEGURIDAD_ERP\Finanzas;


use App\Models\CADECO\Banco;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CtgBanco extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.ctg_bancos';
    public $timestamps = false;


    public function banco()
    {
        return $this->belongsTo(Banco::class, 'id_ctg_bancos');
    }

    public function scopeNoRegistrado($query)
    {
         $bancos = array_column(Banco::query()->select('id_ctg_bancos')->where('id_ctg_bancos', '>', 0)->get()->toArray(),'id_ctg_bancos');
         return $query->whereNotIn('id',$bancos);
    }
}
