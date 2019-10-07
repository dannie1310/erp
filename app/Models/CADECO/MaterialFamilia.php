<?php


namespace App\Models\CADECO;


class MaterialFamilia extends Material
{
    protected $fillable = [
        'descripcion',
        'tipo_material',
        'equivalencia',
        'marca',
        'UsuarioRegistro',
        'FechaHoraRegistro',
        'nivel',
        'numero_parte',
        'tipo',
        'unidad'
    ];
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereRaw('LEN(nivel) = 8');
        });
    }

    public function validarExistente($tipo)
    {

        if($this->where('numero_parte','=', $this->numero_parte)->where('tipo_material','=',1)->get()->toArray() != [])
        {
            throw New \Exception('El material "'.$this->descripcion.'" con el numero de parte "'.$this->numero_parte.'" ya existe.');
        }

    }

    public function nivelConsecutivo($tipo)
    {

        $this->nivel = str_replace ( ".", "", $this->nivel);
//        dd($this->tipo);
        $num = $this->where('tipo_material','=',1)->where('nivel','LIKE',$this->nivel.'.%')->orderBy('nivel', 'desc')->get()->pluck('nivel')->first();

        $num = substr($num, 4,3);
        $num = $num +1;
        $num = str_pad($num, 3, "0", STR_PAD_LEFT);

        return $this->nivel.'.'.$num.'.';
    }
}
