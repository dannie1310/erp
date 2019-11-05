<?php


namespace App\CSV\seguridad;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PermisoUsuario implements FromCollection, WithHeadings
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
                'proyecto'=>$permiso->base_datos,
                'obra'=>$permiso->nombre_obra,
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
            'Proyecto',
            'Obra',
            'Usuario que Asignó',
            'Fecha de Asignación'
        ];
    }

}