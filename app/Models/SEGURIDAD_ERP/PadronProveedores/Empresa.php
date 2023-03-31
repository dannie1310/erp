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
        'id_tipo_empresa',
        'id_tipo_personalidad',
        'usuario_registro'
    ];

    public function giro()
    {
        return $this->belongsTo(CtgGiro::class, 'id_giro', 'id');
    }

    public function especialidades()
    {
        return $this->hasManyThrough(CtgEspecialidad::class, EmpresaEspecialidad::class, 'id_empresa_proveedora', 'id', 'id', 'id_especialidad');
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

    public function contactos()
    {
        return $this->hasMany(Contacto::class,"id_empresa_proveedora", "id");
    }

    public function representantesLegales()
    {
        return $this->hasManyThrough(RepresentanteLegal::class, EmpresaRepresentanteLegal::class, 'id_empresa', 'id', 'id', 'id_representante_legal');
    }

    public function tipoPersonalidad()
    {
        return $this->belongsTo(CtgTipoPersonalidad::class, "id_tipo_personalidad","id" );
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
        return $this->no_archivos_cargados."/". $this->no_archivos_esperados;
    }

    public function getNoArchivosEsperadosAttribute()
    {
        $cantidad_archivos = $this->archivos()->obligatorios()->count();
        if ($this->prestadora->count() == 1){
            $cantidad_archivos_prestadora = $this->prestadora[0]->archivos()->obligatorios($this->id)->count();
            $cantidad_archivos += $cantidad_archivos_prestadora;
        }

        return $cantidad_archivos;
    }

    public function getNoArchivosCargadosAttribute()
    {
        $cantidad_archivos = $this->archivos()->obligatorios()->cargados()->count();
        if ($this->prestadora->count() == 1){
            $cantidad_archivos_prestadora = $this->prestadora[0]->archivos()->obligatorios($this->id)->cargados()->count();
            $cantidad_archivos += $cantidad_archivos_prestadora;
        }
        return $cantidad_archivos;
    }

    public function getTipoEmpresaAttribute(){
        $caracter = substr($this->rfc,3,1);
        if(is_numeric($caracter)){
            return  1;
        } else {
            return  2;
        }
    }

    public function registrar($data){
        try {
            DB::connection('seguridad')->beginTransaction();

            $empresa = $this->create($data);

            if(key_exists("contactos",$data)){
                foreach($data["contactos"] as $contacto)
                {
                    $empresa->contactos()->create($contacto);
                }
            }

            if(key_exists("id_especialidad", $data)){
                array_push($data["id_especialidades"], $data["id_especialidad"]);
            }

            if(count($data["id_especialidades"] )>0){
                foreach($data["id_especialidades"] as $id_especialidad){
                    EmpresaEspecialidad::create(["id_especialidad"=>$id_especialidad, "id_empresa_proveedora"=>$empresa->id]);
                }
            } else {
                abort(500, "Debe existir al menos una especialidad para la empresa.");
            }

            foreach($data["archivos"] as $archivo){
                $empresa->archivos()->create(
                    [
                        "id_tipo_archivo"=>$archivo["id_tipo_archivo"],
                        "obligatorio"=>$archivo["obligatorio"],
                        "complemento_nombre"=>$archivo["complemento_nombre"],
                    ]
                );
            }

            if($empresa->tipo_empresa == 1){
                if(key_exists("representantes_legales",$data)){
                    foreach($data["representantes_legales"] as $representante_legal_data)
                    {
                        $representante_legal = RepresentanteLegal::where("curp",$representante_legal_data["curp"])->first();
                        if(!$representante_legal){
                            $representante_legal = RepresentanteLegal::create($representante_legal_data);
                        }
                        EmpresaRepresentanteLegal::create(["id_representante_legal"=>$representante_legal->id, "id_empresa"=>$empresa->id]);
                    }
                    $empresa->load("representantesLegales");
                    $i=0;
                    foreach($empresa->representantesLegales as $representante_legal){
                        if($i==0){
                            $archivo = $empresa->archivos->where("id_tipo_archivo",3)->first();
                            $archivo->complemento_nombre = $representante_legal->nombre_completo;
                            $archivo->id_representante_legal = $representante_legal->id;
                            $archivo->save();
                        } else {
                            $empresa->archivos()->create(
                                [
                                    "id_tipo_archivo"=>3,
                                    "obligatorio"=>1,
                                    "complemento_nombre"=>$representante_legal->nombre_completo,
                                    "id_representante_legal"=>$representante_legal->id,
                                ]
                            );
                        }
                        $i++;
                    }
                    /*if(!$empresa->representantesLegales()->count()>0)
                    {
                        abort(500, "Debe existir al menos un representante legal para la empresa.");
                    }*/
                }
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
            }
            else{
                if($this->id_tipo_empresa != 3){
                    $this->agregarDatosProveedora($data);
                    if($this->id_tipo_personalidad == 1){
                        if (key_exists("representantes_legales", $data)) {
                            $this->actualizarRepresentantesLegales($data);
                        }
                    }
                    $this->update([
                        'razon_social' => $data['razon_social'],
                        'no_imss' => $data['nss'],
                        'id_giro' => $data['id_giro'],
                    ]);
                }else{
                    $this->update([
                        'razon_social' => $data['razon_social'],
                        'no_imss' => $data['nss'],
                        'rfc' => $data['rfc']
                    ]);
                }
            }
            DB::connection('seguridad')->commit();
            $this->refresh();
            return $this;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
        return $this->hasOne(CtgEstadoExpediente::class, "id","id_estado_expediente" );
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

    private function agregarDatosProveedora($data)
    {
        $ids = array();
        foreach ($data['contactos']['data'] as $contacto) {
            if (array_key_exists('id', $contacto)) {
                array_push($ids, $contacto['id']);
                $this->contactos->find($contacto['id'])->update([
                    'nombre' => $contacto['nombre'],
                    'correo_electronico' => $contacto['correo_electronico'],
                    'telefono' => $contacto['telefono'],
                    'puesto' => $contacto['puesto'],
                    'notas' => $contacto['notas'],
                ]);
            } else {
                $nuevo = $this->contactos()->create($contacto);
                array_push($ids, $nuevo->id);
            }
        }
        $contactos = $this->contactos()->pluck('id');
        $borradas = $contactos->count() > 0 ? array_diff($contactos->toArray(), $ids) : [];
        if ($borradas != []) {
            foreach ($borradas as $id) {
                $this->contactos->find($id)->delete();
            }
        }

        $especialidades = EmpresaEspecialidad::where('id_empresa_proveedora', $this->id)->pluck('id_especialidad');
        $borradas = array_diff($especialidades->toArray(), $data['especialidades_nuevas']);
        $nuevas = array_diff($data['especialidades_nuevas'], $especialidades->toArray());
        if ($especialidades->count() != count($borradas) || count($nuevas) > 0) {
            if ($nuevas) {
                foreach ($nuevas as $id) {
                    EmpresaEspecialidad::create([
                        'id_especialidad' => $id,
                        'id_empresa_proveedora' => $this->id
                    ]);
                }
            }
            if ($borradas) {
                foreach ($borradas as $id) {
                    EmpresaEspecialidad::where('id_especialidad', $id)->where('id_empresa_proveedora', $this->id)->delete();
                }
            }
        }
    }

    private function actualizarRepresentantesLegales($data)
    {
        $ids = array();
        foreach ($data['representantes_legales']['data'] as $representante) {
            if (array_key_exists('id', $representante)) {
                $representante_legal = RepresentanteLegal::find($representante["id"]);
                array_push($ids, $representante['id']);
                $representante_legal->update([
                    'nombre' => $representante['nombre'],
                    'apellido_paterno' => $representante['apellido_paterno'],
                    'apellido_materno' => $representante['apellido_materno'],
                    'es_nacional'      => $representante['es_nacional']
                ]);
                $archivos = $representante_legal->archivos;
                foreach($archivos as $archivo){
                    $archivo->complemento_nombre = $representante_legal->nombre_completo;
                    $archivo->id_representante_legal = $representante_legal->id;
                    $archivo->save();
                }

            } else {
                $representante_legal = RepresentanteLegal::where("curp", $representante["curp"])->first();
                if(!$representante_legal) {
                    $representante_legal = RepresentanteLegal::create($representante);
                }
                EmpresaRepresentanteLegal::create(["id_representante_legal" => $representante_legal->id, "id_empresa" => $this->id]);
                $this->archivos()->create(
                    [
                        "id_tipo_archivo" => 3,
                        "obligatorio" => 1,
                        "complemento_nombre" => $representante_legal->nombre_completo,
                        "id_representante_legal" => $representante_legal->id,
                    ]
                );
                array_push($ids, $representante_legal->id);
            }
        }

        if (array_key_exists('representantes_borrados', $data)) {
            $borradas = $data['representantes_borrados'];
            foreach ($borradas as $id) {
                EmpresaRepresentanteLegal::where('id_representante_legal', '=', $id)->where('id_empresa', '=', $this->id)->delete();
                Archivo::where("id_representante_legal",$id)->where("id_empresa", $this->id)->delete();
                RepresentanteLegal::find($id)->delete();
            }
        }
        if (array_key_exists('representantes_desasociados', $data)) {
            foreach ($data['representantes_desasociados'] as $id) {
                EmpresaRepresentanteLegal::where('id_representante_legal', '=', $id)->where('id_empresa', '=', $this->id)->delete();
                Archivo::where("id_representante_legal",$id)->where("id_empresa", $this->id)->delete();
            }
        }
    }
}
