<?php


namespace App\CSV;
use App\Models\CADECO\Inventarios\InventarioFisico;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;



class InventarioFisicoLayout implements FromCollection, WithHeadings
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
            $folio_marbete= str_pad($marbete->folio,6,0,0);
            $user[]=array(
              "folio"=>$this->inventario->getNumeroFolioFormatAttribute(). ' '.chunk_split($folio_marbete, 3, ' '),
               "id"=>$marbete->id,
            );

        }

        return collect($user);
    }

    public function headings(): array
    {
        return [
            'no_marbete',
            'id_marbete',
            'no_conteo',
            'usados',
            'nuevos',
            'inservibles',
            'total',
            'iniciales',
            'observaciones'
        ];
    }

}
