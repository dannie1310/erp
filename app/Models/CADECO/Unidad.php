<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Unidad extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.unidades';

    public $timestamps = false;
    public $searchable = [
        'unidad',
        'tipo_unidad',
        'descripcion'
    ];

    protected $fillable = [
        'descripcion',
        'unidad'
    ];

    public function validarunidadExistente()
    {
        if($this->where('descripcion','=', $this->descripcion)->get()->toArray() != [])
        {
            throw New \Exception('La descripción "'.$this->descripcion.'" ya se encuentra en el catálogo.');
        }
        if($this->where('unidad','=', $this->unidad)->get()->toArray() != [])
        {
            throw New \Exception('La unidad "'.$this->unidad.'" ya se encuentra en el catálogo.');
        }
    }

    public function unidadComplemento()
    {
        return $this->belongsTo(UnidadComplemento::class, 'unidad', 'unidad');
    }

    public function validarUsoMaterial()
    {
        if(Material::where('unidad', '=', $this->unidad)->first())
            {
                return true;
            }
        return false;
    }

    public function validarUsoItems()
    {
        return (Item::where('unidad','=', $this->unidad)->first() != null) ? true : false;
    }

    public function eliminarUnidad()
    {
        if($this->validarUsoMaterial()){
            abort(403, "\n\n No se puede eliminar la unidad '".$this->unidad."'.\n  La unidad ya esta siendo usada en algunos materiales");
        }
        if($this->validarUsoItems()){
            abort(403, "\n\n No se puede eliminar la unidad '".$this->unidad."'.\n  La unidad ya esta siendo usada en algunas partidas");
        }
        $this->where('unidad', '=', $this->unidad)->delete();
        if($this->unidadComplemento)
        {
            $this->unidadComplemento->delete();
        }
    }

    public function actualizarUnidad($data)
    {
        if($this->validarUsoMaterial()){
            abort(403, "\n\n No se puede editar la unidad '".$this->unidad."'.\n  La unidad ya esta siendo usada en algunos materiales");
        }
        if($this->validarUsoItems()){
            abort(403, "\n\n No se puede editar la unidad '".$this->unidad."'.\n  La unidad ya esta siendo usada en algunas partidas");
        }
        $this->where('unidad', '=', $this->unidad)->update(['unidad' => strtoupper($data['unidad']), 'descripcion' => strtoupper($data['descripcion'])]);
        exit;
    }

    public function buscarPorBase($base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        return $this->get();
    }
}
