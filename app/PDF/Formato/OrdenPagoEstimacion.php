<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 02:59 PM
 */

namespace App\PDF\Formato;


use App\Models\CADECO\Estimacion;
use Ghidev\Fpdf\Rotation;

class OrdenPagoEstimacion extends Rotation
{

    /**
     * OrdenPagoEstimacion constructor.
     * @param Estimacion $estimacion
     */
    public function __construct(Estimacion $estimacion)  //podria recibir un transformer
    {
        parent::__construct('P', 'cm', 'A4');

    }

    function create() {
        $this->SetMargins(1, 0.5, 1);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true,3.75);

        $this->Ln();

        if($this->y > 17.05)
            $this->AddPage();
        $this->Ln(1);

        try {
            $this->Output('I', 'Formato - Orden de Pago Estimación.pdf', 1);
        } catch (\Exception $ex) {
            dd($ex);
        }
        exit;
    }
}