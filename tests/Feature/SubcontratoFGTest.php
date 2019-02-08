<?php

namespace Tests\Feature;

use App\Http\Controllers\v1\AuthController;
use App\Http\Requests\LoginRequest;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Fondo;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use App\Repositories\ContextSession;
use App\Services\AuthService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubcontratoFGTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @dataProvider registraSubcontratoConFGProvider
     */
    public function testRegistraSubcontratoConFondoDeGarantia($datos)
    {
        $subcontrato = Subcontrato::create($datos);
        $this->assertTrue($subcontrato->id_transaccion>0 and $subcontrato->fondo_garantia->id_subcontrato == $subcontrato->id_transaccion );
    }
    /**
     * A basic test example.
     *
     * @dataProvider registraSubcontratoSinFGProvider
     */
    public function testRegistraSubcontratoSinFondoDeGarantia($datos)
    {
        $subcontrato = Subcontrato::create($datos);

        $this->assertTrue($subcontrato->id_transaccion>0 and $subcontrato->fondo_garantia == null);
    }

    /**
     * A basic test example.
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
     * A basic test example.
     *
     * @dataProvider registraSubcontratoSinFGProvider
     */
    public function testRegistraSubcontratoSinRetencionDeFondoDeGarantiaYSeIntentaGenerarUnFondoDeGarantia($datos)
    {
        $this->expectExceptionMessage('La retención de fondo de garantía establecida en el subcontrato no es mayor a 0, el fondo de garantía no puede generarse');
        $subcontrato = Subcontrato::create($datos);
        $fondo_garantia =  new FondoGarantia();
        $fondo_garantia->setRetencionSubcontrato($subcontrato->retencion) ;
        $fondo_garantia->id_subcontrato = $subcontrato->id_transaccion;
        $fondo_garantia->save();
    }



    public function registraSubcontratoConFGProvider(){

        return array (array ( array(
            'id_antecedente'=>2,
            'fecha'=>'2018-01-01',
            'id_empresa'=>2,
            'id_moneda'=>1,
            'anticipo'=>1,
            'anticipo_monto'=>100,
            'anticipo_saldo'=>100,
            'monto'=>100,
            'PorcentajeDescuento'=>1,
            'impuesto'=>16,
            'impuesto_retenido'=>16,
            'id_costo'=>1,
            'retencion'=>10,
            'referencia'=>1,
            'id_obra'=>1,
        ))
        );
    }
    public function registraSubcontratoSinFGProvider(){

        return array (array ( array(
            'id_antecedente'=>2,
            'fecha'=>'2018-01-01',
            'id_empresa'=>2,
            'id_moneda'=>1,
            'anticipo'=>1,
            'anticipo_monto'=>100,
            'anticipo_saldo'=>100,
            'monto'=>100,
            'PorcentajeDescuento'=>1,
            'impuesto'=>16,
            'impuesto_retenido'=>16,
            'id_costo'=>1,
            'retencion'=>0,
            'referencia'=>1,
            'id_obra'=>1,
        ))
        );
    }
}
