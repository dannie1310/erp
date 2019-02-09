<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/02/2019
 * Time: 06:12 PM
 */

namespace Tests\Unit\SubcontratosFG;

use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Transaccion;
use Tests\TestCase;
use App\Models\CADECO\Subcontrato;

class EstimacionTest extends TestCase
{
    public $subcontrato_nuevo;
    public function setUp()
    {
        parent::setUp();

        $this->generaNuevoSubcontrato();


    }
    /**
     * Registro de Estimación con antecedente que no es del tipo esperado
     *
     * @dataProvider registraEstimacionConAntecedenteNoValidoProvider
     */
    public function testRegistraEstimacionConAntecedenteNoValido($datos)
    {
        $this->expectExceptionMessage('La transacción antecedente no es válida');
        $estimacion = Estimacion::create($datos);

    }

    /**
     * Registro de Estimación con retencion de fondo de garantia
     *
     * @dataProvider registraEstimacionConFGProvider
     */

    public function testRegistraEstimacionConRetencionFondoGarantia($datos)
    {
        $datos = array_merge($datos,['id_antecedente'=>$this->subcontrato_nuevo->id_transaccion]);

        $estimacion = Estimacion::create($datos);
        # $estimacion->subcontrato->load('fondo_garantia');

        $this->assertTrue($estimacion->id_transaccion > 0
            && $estimacion->retencion_fondo_garantia->id>0
            && count($estimacion->retencion_fondo_garantia->movimientos->where('id_tipo_movimiento',1))==1
            && $estimacion->subcontrato->fondo_garantia->saldo==$estimacion->retencion_fondo_garantia->importe
        );

    }

    public function testRetencion_fondo_garantia()
    {

    }

    public function testGeneraRetencion()
    {

    }

    /**
     * Data provider con id_antecedente de tipo no válido
     *
     */
    public function registraEstimacionConAntecedenteNoValidoProvider()
    {

        return array(
            array(
                array(
                    'id_antecedente'=>2,
                    'fecha'=>'2019-02-07',
                    'id_obra'=>1,
                    'id_empresa'=>1,
                    'id_moneda'=>1,
                    'cumplimiento'=>'2019-02-10',
                    'vencimiento'=>'2019-02-27',
                    'monto'=>1000,
                    'impuesto'=>160,
                    'anticipo'=>10,
                    'retencion'=>10,
                    'referencia'=>'referencia estimacion',
                    'observaciones'=>'observaciones estimacion',
                )
            )
        );
    }

    /**
     * Data provider con id_antecedente de tipo no válido
     *
     */
    public function registraEstimacionConFGProvider()
    {

        return array(
            array(
                array(

                    'fecha'=>'2019-02-07',
                    'id_obra'=>1,


                    'cumplimiento'=>'2019-02-10',
                    'vencimiento'=>'2019-02-27',
                    'monto'=>1000,
                    'impuesto'=>160,
                    'anticipo'=>10,
                    'retencion'=>10,
                    'referencia'=>'referencia estimacion',
                    'observaciones'=>'observaciones estimacion',
                )
            )
        );
    }

    public function generaNuevoSubcontrato(){
        $subcontrato_nuevo = Subcontrato::create([
            'id_antecedente' => 208188,
            'fecha' => '2018-01-01',
            'id_empresa' => 2,
            'id_moneda' => 1,
            'anticipo' => 1,
            'anticipo_monto' => 100,
            'anticipo_saldo' => 100,
            'monto' => 100,
            'PorcentajeDescuento' => 1,
            'impuesto' => 16,
            'impuesto_retenido' => 16,
            'id_costo' => 1,
            'retencion' => 10,
            'referencia' => 1,
            'id_obra' => 1,]);


        $this->subcontrato_nuevo = $subcontrato_nuevo;
    }

}
