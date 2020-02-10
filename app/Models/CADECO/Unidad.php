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
                abort(403, "\n\n No se puede eliminar y/o editar la unidad '".$this->unidad."'.\n  La unidad ya esta siendo usada en algunos materiales");
            }
    }

    public function eliminarUnidad()
    {
        
        $this->validarUsoItems();
        $this->where('unidad', '=', $this->unidad)->delete();        
        
    }

    public function actualizarUnidad($data)
    {
        $this->validarUsoItems();
        $this->where('unidad', '=', $this->unidad)->update(['unidad' => $data['unidad'], 'descripcion' => $data['descripcion']]);
        dd('');
    }
}
