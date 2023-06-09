<?php


namespace App\CSV\Contabilidad;


use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;



class ContabilidadElectronicaLayout implements FromCollection, WithHeadings
{
    use Exportable;
    protected $datos;


    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        $datos = array();
        foreach ($this->datos['partidas'] as $key => $partida){
            $datos[] = array(
                'num' => $key+1,
                'codigo_cuenta' => $partida['codigo_cuenta'],
                'numero_cuenta' => $partida['numero_cuenta'],
                'naturaleza' => $partida['naturaleza'],
                'saldo_inicial' => $partida['saldo'],
                'debe' => $partida['debe'],
                'haber' => $partida['haber'],
                'saldo_final' => $partida['saldo_total'],
            );
        }
        return collect($datos);
    }

    public function headings(): array
    {
        return array([
            '   ',
            'RFC                 ',
            $this->datos['rfc'],
            '                     ',
            'Mes                  ',
            $this->datos['mes'],
            '                     ',
            'Anio                 ',
            $this->datos['anio'],
            '                      ',
            '                      ',
            '                        ',
            '   ',
            '   ',
            '   ',
            '    ',
            '   ',
            '   ',
            '   ',
        ], ['#',
            'Codigo cuenta',
            'Cuenta',
            'Naturaleza',
            'Saldo Inicial',
            'Debe',
            'Haber',
            'Saldo Final',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',]);
    }
}
