<?php


namespace App\Models\CADECO;


class Familia extends Material
{
    protected $fillable = [
        'descripcion',
        'tipo_material',
        'equivalencia',
        'marca',
        'UsuarioRegistro',
        'FechaHoraRegistro',
        'nivel'
    ];
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereRaw('LEN(nivel) = 4');
        });
    }

    public function validarExistente($tipo)
    {
        if($this->where('descripcion','=', $this->descripcion)->where('tipo_material','=',$tipo)->get()->toArray() != [])
        {
            throw New \Exception('Esta descripción "'.$this->descripcion.'" ya existe.');
        }
    }

    public function scopeMarca($query, $tipo)
    {

        return $query->where('tipo_material','=',$tipo);
//        ->whereIn('tipo_material', explode(",", $tipo));
////        return $query->whereIn('tipo_material', explode(",", 2))->where('marca','=',$marca);
//        $consulta =  Material::query()->whereRaw("nivel LIKE '".$model->nivel."0%'")->where('tipo_material','=',2)->where('marca','=',1)->pluck('descripcion')->first();
//        return $query->where('marca','=',1);
    }

    public function nivelConsecutivo($tipo)
    {
        $n = 0;
        foreach ($this->where('tipo_material','=',$tipo)->orderBy('nivel', 'asc')->get()->pluck('nivel') as $nivel){
            if((int)$nivel != $n) {
                return str_pad($n, 3, 0, 0) . '.';
            }
            $n++;
        }
        return str_pad($n, 3, 0, 0) . '.';
    }
}
