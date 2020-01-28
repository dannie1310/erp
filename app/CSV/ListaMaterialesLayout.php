<?php


namespace App\CSV;


use App\Models\CADECO\Material;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ListaMaterialesLayout implements FromCollection, WithHeadings
{
    use Exportable;
    protected $material;

    public function __construct(Material $material)
    {
        $this->material = $material;
    }

    public function collection()
    {
        $insumos = array();
        foreach($this->material->requisicionInsumos() as $insumo)
        {
            $insumos[] = array(
                'id' => $insumo->id_material,
                'numero_parte' => $insumo->numero_parte,
                'Descripcion' => $insumo->descripcion,
                'unidad' => $insumo->unidad
            );
        }return collect($insumos);
    }
    
    public function headings(): array
    {
         return array(['ID. Material',
                       'Numero de parte',
                       'Descripcion',
                       'Unidad']);
    }
}