<?php



namespace App\Models\CADECO;

use Illuminate\Database\Eloquent\Model;

class Basico extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.basicos';
    protected $primaryKey = 'id_basico';

    public $timestamps = false;
}