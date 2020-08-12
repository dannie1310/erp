<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


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
            $this->update([
                'razon_social' => $data['razon_social'],
                'no_imss' => $data['nss'],
                'id_giro' => $data['id_giro'],
                'id_especialidad' => $data['id_especialidad'],
                'nombre_contacto' => $data['contacto'],
                'telefono' => $data['telefono'],
                'correo_electronico' => $data['correo'],
                'rfc' => $data['rfc']
            ]);
            DB::connection('seguridad')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }
}
