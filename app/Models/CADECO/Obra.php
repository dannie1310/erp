<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 6/12/18
 * Time: 04:23 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Contabilidad\DatosContables;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use Illuminate\Database\Eloquent\Model;

class Obra extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'obras';
    protected $primaryKey = 'id_obra';

    public $timestamps = false;

    protected $hidden = ['logo'];

    public function datosContables()
    {
        return $this->hasOne(DatosContables::class, 'id_obra');
    }

    public function configuracionObra(){
        return $this->hasOne(ConfiguracionObra::class, 'id_obra', 'id_obra');
    }

    public function getLogoAttribute()
    {
        if(isset($this->configuracionObra->logotipo_original)){
            return $this->configuracionObra->logotipo_original;
        }else{
            $file = public_path('img/ghi-logo.png');
            $data = unpack("H*", file_get_contents($file));
            return bin2hex($data[1]);
        }
    }
}