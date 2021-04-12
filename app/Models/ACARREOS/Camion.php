<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Camion extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'camiones';
    public $primaryKey = 'IdCamion';

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
            $this->IdSindicato = $data['id_sindicato'];
            $this->IdEmpresa = $data['id_empresa'];
            $this->Propietario = $data['propietario'];
            $this->IdOperador = $data['id_operador'];
            $this->PlacasCaja = $data['placa_caja'];
            $this->IdMarca = $data['id_marca'];
            $this->Modelo = $data['modelo'];
            $this->PolizaSeguro = $data['poliza_seguro'];
            $this->VigenciaPolizaSeguro = $data['vigencia_poliza'];
            $this->Aseguradora = $data['aseguradora'];
            $this->Ancho = $data['ancho'];
            $this->Largo = $data['largo'];
            $this->Alto = $data['alto'];
            $this->AlturaExtension = $data['altura_extension'];
            $this->EspacioDeGato = $data['espacio_gato'];
            $this->Disminucion = $data['disminucion'];
            $this->CubicacionReal = $data['cubicacion_real'];
            $this->CubicacionParaPago = $data['cubicacion_pago'];

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
