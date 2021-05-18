<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 28/02/2020
 * Time: 05:46 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOSCambio;
use App\Models\SEGURIDAD_ERP\Fiscal\NoLocalizado;
use Illuminate\Database\Eloquent\Model;

class CargaCFDSAT extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cargas_cfd_sat';
    public $timestamps = false;
    protected $fillable =[
        "nombre_archivo_zip"
        ,"archivos_leidos"
        ,"archivos_cargados"
        ,"archivos_no_cargados"
        ,"archivos_preexistentes"
        ,"archivos_receptor_no_valido"
        ,"archivos_no_cargados_error_app"
        ,"archivos_corruptos"
        ,"archivos_tipo_incorrecto"
        ,"proveedores_nuevos"
        ,"fecha_hora_fin"
        ,"usuario_cargo"
        ,"cfd_cargados"
        ,"cfd_no_cargados"
        ,"cfd_receptor_no_valido"
        ,"cfd_preexistentes"
        ,"cfd_no_cargados_error_app"
    ];

    public function cfd()
    {
        return $this->hasMany(CFDSAT::class, 'id_carga_cfd_sat', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_cargo', 'idusuario');
    }

    public function cambios()
    {
        return $this->hasMany(EFOSCambio::class, "id_carga_cfd", "id");
    }

    public function noLocalizados()
    {
        return $this->hasMany(NoLocalizado::class, "id_carga_cfd", "id");
    }

}
