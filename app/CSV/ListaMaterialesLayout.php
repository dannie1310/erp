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
    protected $id;

    public function __construct(Material $material, $id)
    {
        $this->material = $material;
        $this->id = $id;
    }

    public function collection()
    {
        $insumos = array();
        if(is_numeric($this->id))
        {
            $materiales = $this->material->materialesPorAlmacen($this->id);
        }else{
            $materiales= $this->material->requisicionInsumos();
        }
        foreach($materiales as $insumo)
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
