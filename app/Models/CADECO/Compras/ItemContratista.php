<?php


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class ItemContratista extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.ItemsXContratista';
    protected $primaryKey = 'id_item';

    public $timestamps = false;

}