<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Empresa extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.empresas';
    public $timestamps = false;

    protected $fillable = [
        'razon_social',
        'rfc',
        'no_imss',
        'id_giro',
        'id_especialidad',
        'id_tipo_empresa',
        'nombre_contacto',
        'telefono',
        'correo_electronico'
    ];

    public function giro()
    {
        return $this->belongsTo(CtgGiro::class, 'id_giro', 'id');
    }

    public function especialidad()
    {
        return $this->belongsTo(CtgEspecialidad::class, 'id_especialidad', 'id');
    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipoEmpresa::class, 'id_tipo_empresa', 'id');
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class, "id_empresa", "id");
    }

    public function prestadora()
    {
        return $this->hasManyThrough(Empresa::class, EmpresaPrestadora::class, 'id_empresa_proveedor', 'id', 'id', 'id_empresa_prestadora');
    }

    public function proveedor()
    {
        return $this->hasManyThrough(Empresa::class, EmpresaPrestadora::class, 'id_empresa_prestadora', 'id', 'id', 'id_empresa_proveedor');
    }

    public function estado_expediente()
    {
        return $this->hasOne(CtgEstadoExpediente::class, "id","id_estado_expediente" );
    }

    public function usuario_inicio()
    {
        return $this->belongsTo(Usuario::class, "usuario_registro","idusuario" );
    }

    public function registrar($data){
        try {
            DB::connection('seguridad')->beginTransaction();

            $empresa = $this->create($data);

            foreach($data["archivos"] as $archivo){
                $empresa->archivos()->create(["id_tipo_archivo"=>$archivo->id_tipo_archivo]);
            }

            DB::connection('seguridad')->commit();
            return $empresa;

        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function editar($data)
    {
        try {
            DB::connection('seguridad')->beginTransaction();
            if(array_key_exists('cambio_prestadora', $data)){
                $this->cambiarPrestadora($data['id_proveedor'], $data['id']);
            }else {
                $this->update([
                    'razon_social' => $data['razon_social'],
                    'no_imss' => $data['nss'],
                    'id_giro' => $data['id_giro'],
                    'id_especialidad' => $data['id_especialidad'],
                    'nombre_contacto' => array_key_exists('contacto',$data) ? $data['contacto'] : null,
                    'telefono' => array_key_exists('telefono', $data) ? $data['telefono'] : null,
                    'correo_electronico' => array_key_exists('correo', $data) ? $data['correo'] : null,
                    'rfc' => $data['rfc']
                ]);
            }
            DB::connection('seguridad')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
        return $this->hasOne(CtgEstadoExpediente::class, "id","id_estado_expediente" );
    }

    public function usuario_inicio()
    {
        return $this->belongsTo(Usuario::class, "usuario_registro","idusuario" );
    }

    public function scopeProveedores($query)
    {
        return $query->whereIn("id_tipo_empresa", [1,2]);
    }

    public function getPorcentajeAvanceExpedienteAttribute()
    {
        return number_format($this->no_archivos_cargados/ $this->no_archivos_esperados*100,0,"","");
    }

    public function getColorBarraAttribute()
    {
        if($this->porcentaje_avance_expediente>=0 && $this->porcentaje_avance_expediente<=50)
        {
            return "bg-danger";
        }
        else if($this->porcentaje_avance_expediente>50 && $this->porcentaje_avance_expediente<=99)
        {
            return "bg-warning";
        }
        else if($this->porcentaje_avance_expediente==100)
        {
            return "bg-success";
        }
    }

    public function getAvanceExpedienteAttribute()
    {
       /*$cantidad_archivos = $this->archivos->count();
        $cantidad_archivos_cargados = $this->archivos()->cargados()->count();*/
        return $this->no_archivos_cargados."/". $this->no_archivos_esperados;
    }

    public function getNoArchivosEsperadosAttribute()
    {
        $cantidad_archivos = $this->archivos()->obligatorios()->count();
        return $cantidad_archivos;
    }

    public function getNoArchivosCargadosAttribute()
    {
        $cantidad_archivos = $this->archivos()->obligatorios()->cargados()->count();
        return $cantidad_archivos;
    }

    public function getPorcentajeAvanceExpedienteAttribute()
    {
        return number_format($this->no_archivos_cargados/ $this->no_archivos_esperados*100,2,".","");
    }

    private function cambiarPrestadora($id_proveedor, $id_prestadora)
    {
        $prestadora = EmpresaPrestadora::where("id_empresa_prestadora","=",$id_prestadora)
            ->where("id_empresa_proveedor","=",$id_proveedor)
            ->first();
        $prestadora->update([
            'id_empresa_prestadora' => $this->id
        ]);
    }
}
