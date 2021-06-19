<?php


namespace App\Models\CTPQ;


use Illuminate\Database\Eloquent\Model;

class AsocCFDI extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'AsocCFDIs';
    protected $primaryKey = 'Id';
    protected $fillable =[
        'UUID',
        'Referencia',
        'AppType',
        'Reconstruir'
    ];

    public $timestamps = false;

    public static function getUltimoFolio()
    {
        $folio = AsocCFDI::orderBy("Id","desc")->first()->pluck("Id");
        return $folio+1;
    }

}
