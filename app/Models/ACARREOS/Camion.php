<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Camion extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'camiones';
    public $primaryKey = 'IdCamion';

    protected $fillable = [
        'IdSindicato',
        'IdEmpresa',
        'Propietario',
        'IdOperador',
        'PlacasCaja',
        'IdMarca',
        'VigenciaPolizaSeguro',
        'Aseguradora',
        'Ancho',
        'EspacioDeGato',
        'Disminucion',
        'CubicacionReal',
        'CubicacionParaPago',
    ];

    /**
     * Relaciones Eloquent
     */
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'IdMarca', 'IdMarca');
    }

    public function sindicato()
    {
        return $this->belongsTo(Sindicato::class, 'IdSindicato', 'IdSindicato');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'IdEmpresa', 'IdEmpresa');
    }

    public function operador()
    {
        return $this->belongsTo(Operador::class, 'IdOperador', 'IdOperador');
    }

    public function registro()
    {
        return $this->belongsTo(Usuario::class,  'usuario_registro', 'idusuario');
    }

    public function desactivo()
    {
        return $this->belongsTo(Usuario::class,  'usuario_desactivo', 'idusuario');
    }

    public function imagenes()
    {
        return $this->hasMany(CamionImagen::class, 'IdCamion', 'IdCamion');
    }

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('camiones.Estatus',  1);
    }

    public function scopeMarcaDescripcion($query)
    {
        return $query->leftjoin('marcas','marcas.IdMarca', 'camiones.IdMarca');
    }

    /**
     * Attributes
     */
    public function getDescripcionMarcaAttribute()
    {
        try{
           return $this->marca->Descripcion;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getNombreOperadorAttribute()
    {
        try{
            return $this->operador->Nombre;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->Estatus)
        {
            case 1:
                return 'ACTIVO';
                break;
            case 0:
                return 'INACTIVO';
                break;
            default:
                return '';
                break;
        }
    }

    public function getColorEstadoAttribute()
    {
        switch ($this->Estatus)
        {
            case 1:
                return '#00a65a';
                break;
            case 0:
                return '#706e70';
                break;
            default:
                return '#d1cfd1';
                break;
        }
    }

    public function getFechaRegistroAttribute()
    {
        return $this->FechaAlta.' '.$this->HoraAlta;
    }

    public function getNombreRegistroAttribute()
    {
        try{
            return $this->registro->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getNombreDesactivoAttribute()
    {
        try{
            return $this->desactivo->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getDescripcionSindicatoAttribute()
    {
        try{
            return $this->sindicato->Descripcion;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getRazonSocialEmpresaAttribute()
    {
        try{
            return $this->empresa->razonSocial;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getFechaDesactivacionFormatAttribute()
    {
        $date = date_create($this->updated_at);
        return date_format($date,"d/m/Y H:i");
    }

    /**
     * Métodos
     */
    public function activar()
    {
        try {
            DB::connection('acarreos')->beginTransaction();
            $this->Estatus = 1;
            $this->usuario_desactivo = NULL;
            $this->motivo = NULL;
            $this->save();
            DB::connection('acarreos')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function desactivar($motivo)
    {
        try {
            DB::connection('acarreos')->beginTransaction();
            $this->Estatus = 0;
            $this->usuario_desactivo = auth()->id();
            $this->motivo = $motivo;
            $this->save();
            DB::connection('acarreos')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function editar($data)
    {
        try {
            DB::connection('acarreos')->beginTransaction();
            $fecha = New DateTime($data['vigencia_poliza']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $this->update([
                'IdSindicato' => $data['id_sindicato'],
                'IdEmpresa' => $data['id_empresa'],
                'Propietario' => $data['propietario'],
                'IdOperador' => $data['id_operador'],
                'PlacasCaja' => $data['placa_caja'],
                'IdMarca' => $data['id_marca'],
                'Modelo' => $data['modelo'],
                'PolizaSeguro' => $data['poliza_seguro'],
                'VigenciaPolizaSeguro' => $fecha->format("Y-m-d"),
                'Aseguradora' => $data['aseguradora'],
                'Ancho' => $data['ancho'],
                'Largo' => $data['largo'],
                'Alto' => $data['alto'],
                'AlturaExtension' => $data['altura_extension'],
                'EspacioDeGato' => $data['espacio_gato'],
                'Disminucion' => $data['disminucion'],
                'CubicacionReal' => $data['cubicacion_real'],
                'CubicacionParaPago' => $data['cubicacion_pago']
            ]);
            if($data['imagenes'] != [])
            {
                $this->editarImagenes($data['imagenes']);
            }
            DB::connection('acarreos')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    private function editarImagenes($imagenes)
    {
        /*
         * Imagen Frente
         */
        if(array_key_exists('id_frente', $imagenes) && $imagenes['id_frente'] == '' && array_key_exists('frente', $imagenes) &&  (array_key_exists('frente', $imagenes) && $imagenes['frente'] == ''))
        {
            $imagen = CamionImagen::where('IdCamion', $this->IdCamion)
                ->where('TipoC', 'f')->first();
            $imagen->cancelarImagen($imagen);
        }

        if((!array_key_exists('id_frente', $imagenes) || (array_key_exists('id_frente', $imagenes) && $imagenes['id_frente'] == '')) && $imagenes['frente'] != '')
        {
            CamionImagen::create([
                'IdCamion' => $this->getKey(),
                'TipoC' => 'f',
                'Imagen' => $imagenes['frente'],
                'Tipo' => $imagenes['tipo_frente']
            ]);
        }

        /*
         * Imagen Derecha
         */
        if(array_key_exists('id_derecha', $imagenes) && $imagenes['id_derecha'] == '' && array_key_exists('id_derecha', $imagenes) && (array_key_exists('derecha', $imagenes) && $imagenes['derecha'] == ''))
        {
            $imagen = CamionImagen::where('IdCamion', $this->IdCamion)
                ->where('TipoC', 'd')->first();
            $imagen->cancelarImagen($imagen);
        }

        if((!array_key_exists('id_derecha', $imagenes) || (array_key_exists('id_derecha', $imagenes) && $imagenes['id_derecha'] == '')) && $imagenes['derecha'] != '')
        {
            CamionImagen::create([
                'IdCamion' => $this->getKey(),
                'TipoC' => 'd',
                'Imagen' => $imagenes['derecha'],
                'Tipo' => $imagenes['tipo_derecha']
            ]);
        }

        /*
         * Imagen Atrás
         */
        if(array_key_exists('id_atras', $imagenes) && $imagenes['id_atras'] == '' && array_key_exists('atras', $imagenes) && $imagenes['atras'] == '')
        {
            $imagen = CamionImagen::where('IdCamion', $this->IdCamion)
                ->where('TipoC', 'f')->first();
            $imagen->cancelarImagen($imagen);
        }

        if((!array_key_exists('id_atras', $imagenes) || (array_key_exists('id_atras', $imagenes) && $imagenes['id_atras'] == '')) && (array_key_exists('atras', $imagenes) && $imagenes['atras'] != ''))
        {
            CamionImagen::create([
                'IdCamion' => $this->getKey(),
                'TipoC' => 't',
                'Imagen' => $imagenes['atras'],
                'Tipo' => $imagenes['tipo_atras']
            ]);
        }

        /*
        * Imagen Izquierda
        */
        if(array_key_exists('id_izquierda', $imagenes) && $imagenes['id_izquierda'] == '' && array_key_exists('izquierda', $imagenes) && $imagenes['izquierda'] == '')
        {
            $imagen = CamionImagen::where('IdCamion', $this->IdCamion)
                ->where('TipoC', 'f')->first();
            $imagen->cancelarImagen($imagen);
        }

        if((!array_key_exists('id_izquierda', $imagenes) || (array_key_exists('id_izquierda', $imagenes) && $imagenes['id_izquierda'] == '')) && (array_key_exists('izquierda', $imagenes) && $imagenes['izquierda'] != ''))
        {
            CamionImagen::create([
                'IdCamion' => $this->getKey(),
                'TipoC' => 't',
                'Imagen' => $imagenes['izquierda'],
                'Tipo' => $imagenes['tipo_izquierda']
            ]);
        }
    }
}
