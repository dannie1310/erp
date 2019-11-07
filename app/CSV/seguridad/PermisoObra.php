<?php


namespace App\CSV\seguridad;


use App\Models\SEGURIDAD_ERP\Permiso;
use App\Services\SEGURIDAD_ERP\PermisoService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PermisoObra implements FromCollection, WithHeadings
{
    protected $permisos;

    public function __construct($permisos)
    {
        $this->permisos=$permisos;

    }

    public function collection()
    {
       $lista= array();
       foreach ($this->permisos as $permiso){
           $lista[]=array(
               'permiso' =>$permiso->permiso,
               'rol'=>$permiso->rol,
               'sistema'=>$permiso->sistema,
               'usuario'=>$permiso->usuario,
               'usuario_nombre'=>$permiso->nombre_completo_usuario,
               'usuario_asgino'=>$permiso->usuario_asigno,
               'fecha'=>$permiso->fecha_hora_asignacion,

           );
       }

       return collect($lista);
    }

    public function headings(): array
    {
        return [
            'Permiso',
            'Rol',
            'Sistema',
            'Usuario',
            'Nombre de Usuario',
            'Usuario que Asignó',
            'Fecha de Asignación'
        ];
    }
}
