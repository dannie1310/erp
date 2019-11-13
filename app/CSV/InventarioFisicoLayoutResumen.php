<?php


namespace App\CSV;


use App\Models\CADECO\Inventarios\Conteo;
use App\Models\CADECO\Inventarios\InventarioFisico;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventarioFisicoLayoutResumen implements FromCollection, WithHeadings
{
    use Exportable;
    protected $inventario;


    public function __construct(InventarioFisico $inventario)
    {
        $this->inventario = $inventario;

    }
    public function collection()
    {
        $user = array();
        foreach ($this->inventario->marbetes as $marbete){
            $conteos = array();
            foreach ($marbete->conteos as $conteo){
                $conteos[$conteo->tipo_conteo] = $conteo;
            }
            $folio_marbete= str_pad($marbete->folio,6,0,0);
            $user[]=array(
                "folio"=>$this->inventario->getNumeroFolioFormatAttribute(). ' '.chunk_split($folio_marbete, 3, ' '),
                "almacen"=>$marbete->almacen->descripcion,
                "id_material" => $marbete->id_material,
                "material"=>$marbete->material->descripcion,
                "unidad" => $marbete->unidad,
                "saldo"=>$marbete->saldo,
                "precio_unitario" => number_format((float) ($marbete->precio_unitario != 0 ? $marbete->precio_unitario : $marbete->precioUnitarioPromedio()), 2, '.', ''),
                "nuevo1"=>array_key_exists('1',$conteos)?$conteos[1]->cantidad_nuevo:'  ',
                "usado1"=>array_key_exists('1',$conteos)?$conteos[1]->cantidad_usados:'  ',
                "inservible1"=>array_key_exists('1',$conteos)?$conteos[1]->cantidad_inservible:'  ',
                "total1"=>array_key_exists('1',$conteos)?$conteos[1]->total:'  ',
                "nuevo2"=>array_key_exists('2',$conteos)?$conteos[2]->cantidad_nuevo:'  ',
                "usado2"=>array_key_exists('2',$conteos)?$conteos[2]->cantidad_usados:'  ',
                "inservible2"=>array_key_exists('2',$conteos)?$conteos[2]->cantidad_inservible:'  ',
                "total2"=>array_key_exists('2',$conteos)?$conteos[2]->total:'  ',
                "nuevo3"=>array_key_exists('3',$conteos)?$conteos[3]->cantidad_nuevo:'  ',
                "usado3"=>array_key_exists('3',$conteos)?$conteos[3]->cantidad_usados:'  ',
                "inservible3"=>array_key_exists('3',$conteos)?$conteos[3]->cantidad_inservible:'  ',
                "total3"=>array_key_exists('3',$conteos)?$conteos[3]->total:'  ',
            );
        }
        return collect($user);
    }

    public function headings(): array
    {
        return array([
            '   ',
            '   ',
            '   ',
            '   ',
            '   ',
            '   ',
            '   ',
            'Conteo1',
            '   ',
            '   ',
            '   ',
            'Conteo2',
            '   ',
            '   ',
            '   ',
            'Conteo3',
            '   ',
            '   ',
            '   ',
        ], ['No. Marbete',
            'Almacen',
            'Id Material',
            'Material',
            'Unidad',
            'Saldo',
            'Precio Unitario',
            'usados',
            'nuevos',
            'inservibles',
            'total',
            'usados',
            'nuevos',
            'inservibles',
            'total',
            'usados',
            'nuevos',
            'inservibles',
            'total',]);
    }
}
