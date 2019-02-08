<?php

namespace Tests\Feature;

use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use Tests\TestCase;

class SubcontratoFGTest extends TestCase
{
    /**
     * Registro de Subcontrato con retención de fondo de garantía, que DEBE detonar el registro de un fondo de garantía
     *
     * @dataProvider registraSubcontratoConFGProvider
     */
    public function testRegistraSubcontratoConFondoDeGarantia($datos)
    {
        $subcontrato = Subcontrato::create($datos);
        $this->assertTrue($subcontrato->id_transaccion > 0 and $subcontrato->fondo_garantia->id_subcontrato == $subcontrato->id_transaccion);
    }

    /**
     * Registro de Subcontrato sin retención de fondo de garantía, que NO DEBE detonar el registro de un fondo de garantía
     *
     * @dataProvider registraSubcontratoSinFGProvider
     */
    public function testRegistraSubcontratoSinFondoDeGarantia($datos)
    {
        $subcontrato = Subcontrato::create($datos);
        $this->assertTrue($subcontrato->id_transaccion > 0 and $subcontrato->fondo_garantia == null);
    }

    /**
     * Registro de Subcontrato sin retención de fondo de garantía, posteriormente se llama a su método para generar
     * Fondo de garantía y este debe lanzar una excepción
     *
     * @dataProvider registraSubcontratoSinFGProvider
     */
    public function testRegistraSubcontratoSinRetencionDeFondoDeGarantiaYPosteriormenteSeLePideRegistroDeFondoDeGarantia($datos)
    {
        $this->expectExceptionMessage('El subcontrato no tiene retención de fondo de garantía');
        $subcontrato = Subcontrato::create($datos);
        $subcontrato->generaFondoGarantia();
    }

    /**
     * Registro de Subcontrato sin retención de fondo de garantía, posteriormente se trata de registrar un fondo de garantía
     * asociado a ese subcontrato y debe lanzar una excepción
     *
     * @dataProvider registraSubcontratoSinFGProvider
     */
    public function testRegistraSubcontratoSinRetencionDeFondoDeGarantiaYSeIntentaGenerarUnFondoDeGarantia($datos)
    {
        $this->expectExceptionMessage('La retención de fondo de garantía establecida en el subcontrato no es mayor a 0, el fondo de garantía no puede generarse');
        $subcontrato = Subcontrato::create($datos);
        $fondo_garantia = new FondoGarantia();
        $fondo_garantia->id_subcontrato = $subcontrato->id_transaccion;
        $fondo_garantia->save();
    }

    public function registraSubcontratoConFGProvider()
    {

        return array(
            array(
                array(
                    'id_antecedente' => 2,
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
                    'id_obra' => 1,
                    )
                )
            );
    }

    public function registraSubcontratoSinFGProvider()
    {

        return array(
            array(
                array(
                    'id_antecedente' => 2,
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
                    'retencion' => 0,
                    'referencia' => 1,
                    'id_obra' => 1,
                    )
                )
            );
    }
}
