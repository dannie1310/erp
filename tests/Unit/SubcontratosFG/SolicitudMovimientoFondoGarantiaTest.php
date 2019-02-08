<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/02/2019
 * Time: 01:02 PM
 */

namespace Tests\Unit\SubcontratosFG;

use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use App\Models\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia;
use Tests\TestCase;
use App\Models\CADECO\SubcontratosFG\MovimientoSolicitudMovimientoFondoGarantia;

class SolicitudMovimientoFondoGarantiaTest extends TestCase
{

    private $fondo;
    private $fondo_nuevo;

    public function setUp()
    {
        parent::setUp();

        $this->fondo = FondoGarantia::first();
        $this->generaNuevoFondo();

    }
    /**
     * Validar excepción por solicitudes pendientes de autorizar
     * @dataProvider datosSolicitudMovimientoFG
     */

    public function testExcepcionGeneraSolicitudCuandoHaySolicitudesPendientes($datos)
    {
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo->id_subcontrato]);
        $this->expectExceptionMessage('Hay una solicitud de movimiento a fondo de garantía pendiente de autorizar, la solicitud actual no puede registrarse');
        SolicitudMovimientoFondoGarantia::create($datos);
        SolicitudMovimientoFondoGarantia::create($datos);
    }

    /**
     * Validar excepción por intentar registrar movimiento de registro de solicitud más de una vez
     * @dataProvider datosSolicitudMovimientoFG
     */

    public function testExcepcionGenerarSolicitudConDosMovimientosDeRegistro($datos)
    {
        $this->generaNuevoFondo();
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_nuevo->id_subcontrato]);
        $this->expectExceptionMessage('Ya existe un movimiento del mismo tipo, el movimiento no puede registrarse');
        $solicitud = SolicitudMovimientoFondoGarantia::create($datos);
        MovimientoSolicitudMovimientoFondoGarantia::create([
                'id_solicitud'=>$solicitud->id,
                'id_tipo_movimiento'=>1,
                'usuario_registra'=>$solicitud->usuario_registra
            ]
        );

    }

    /**
     * Registro de solicitud de movimiento de fondo de garantía y su movimiento de registro
     * @dataProvider datosSolicitudMovimientoFG
*/

    public function testGeneraSolicitudMovimientoDeFondoDeGarantiaYSuMovimientoDeRegistro($datos)
    {
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_nuevo->id_subcontrato]);
        $solicitud = SolicitudMovimientoFondoGarantia::create($datos);
        $this->assertTrue($solicitud->id > 0 && count($solicitud->movimientos->where('id_tipo_movimiento',1))==1);

    }

    /**
     * Data provider para registro de solicitud
     *
     */
    public function datosSolicitudMovimientoFG()
    {

        return array(
            array(
                array(
                    'id_tipo_solicitud'=>1,
                    'fecha'=>'2019-02-08',
                    'referencia'=>'Test',
                    'importe'=>1000,
                    'observaciones'=>'observaciones de prueba',
                    'usuario_registra'=>180
                )
            )
        );
    }
    public function generaNuevoFondo(){
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

        $this->fondo_nuevo = $subcontrato_nuevo->fondo_garantia;
    }
}
