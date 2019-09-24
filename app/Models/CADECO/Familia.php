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
