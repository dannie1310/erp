<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.unidades';

    public $timestamps = false;
    public $searchable = [
        'unidad',
        'tipo_unidad',
        'descripcion',
        'IdUsuario'
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

    public function validarUsoItems()
    {
        if(Material::where('unidad', '=', $this->unidad)->first())
            {
                return true;
            }
        return false;
    }

    public function eliminarUnidad()
    {
        if($this->validarUsoItems()){
            abort(403, "\n\n No se puede eliminar la unidad '".$this->unidad."'.\n  La unidad ya esta siendo usada en algunos materiales");
        }
        $this->where('unidad', '=', $this->unidad)->delete();        
        
    }

    public function actualizarUnidad($data)
    {
        if($this->validarUsoItems()){
            abort(403, "\n\n No se puede editar la unidad '".$this->unidad."'.\n  La unidad ya esta siendo usada en algunos materiales");
        }
        $this->where('unidad', '=', $this->unidad)->update(['unidad' => strtoupper($data['unidad']), 'descripcion' => strtoupper($data['descripcion'])]);
    }
}
