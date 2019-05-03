<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/02/2019
 * Time: 01:02 PM
 */

namespace Tests\Unit\SubcontratosFG;

use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use App\Models\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia;
use Tests\TestCase;
use App\Models\CADECO\SubcontratosFG\MovimientoSolicitudMovimientoFondoGarantia;

class SolicitudMovimientoFondoGarantiaTest extends TestCase
{

    private $fondo;
    private $fondo_nuevo;
    private $fondo_afectable_nuevo;
    private $solicitud_para_autorizar;

    public function setUp()
    {
        parent::setUp();

        $this->fondo = FondoGarantia::first();
        $this->generaNuevoFondo();
        $this->generaNuevoFondoAfectable();

    }
    /**
     * @dataProvider datosSolicitudMovimientoLiberacionFG
     */
    public function testRevierteAutorizacionSolicitudLiberacion($datos)
    {
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_afectable_nuevo->id_subcontrato]);
        $datos['observaciones'] = 'observaciones test revertir autorización';
        $solicitud = SolicitudMovimientoFondoGarantia::create($datos);
        $solicitud->autorizar(["id_usuario"=>180]);
        $movimientos_autorizacion = $solicitud->movimientos->where('id_tipo_movimiento',2);
        $movimiento_autorizacion = $movimientos_autorizacion[1];
        $solicitud->revertirAutorizacion(["id_usuario"=>180, 'observaciones'=>"Error al autorizar"]);
        $movimientos_revertir_autorizacion = $solicitud->movimientos->where('id_tipo_movimiento',5);
        $movimiento_revertir_autorizacion = $movimientos_revertir_autorizacion[2];

        $this->assertTrue(
            $solicitud->estado == -3  &&
            count($solicitud->movimientos->where('id_tipo_movimiento',5))==1 &&
            $movimiento_autorizacion->movimiento_fondo_garantia->transaccion_generada->tipo_transaccion == 53 &&
            $movimiento_autorizacion->movimiento_fondo_garantia->transaccion_generada->estado == -2 &&
            $movimiento_autorizacion->movimiento_fondo_garantia->transaccion_generada->saldo == 0 &&
            $movimiento_autorizacion->movimiento_fondo_garantia->fondo_garantia->saldo == 100 &&
            $movimiento_revertir_autorizacion->movimiento_antecedente->id == $movimiento_autorizacion->id
        );
    }

    /**
     * @dataProvider datosSolicitudMovimientoDescuentoFG
     */
    public function testRevierteAutorizacionSolicitudDescuento($datos)
    {
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_afectable_nuevo->id_subcontrato]);
        $datos['observaciones'] = 'observaciones test revertir descuento';
        $solicitud = SolicitudMovimientoFondoGarantia::create($datos);
        $solicitud->autorizar(["id_usuario"=>180]);
        $movimientos_autorizacion = $solicitud->movimientos->where('id_tipo_movimiento',2);
        $movimiento_autorizacion = $movimientos_autorizacion[1];
        $solicitud->revertirAutorizacion(["id_usuario"=>180, 'observaciones'=>"Error al autorizar descuento"]);
        $movimientos_revertir_autorizacion = $solicitud->movimientos->where('id_tipo_movimiento',5);
        $movimiento_revertir_autorizacion = $movimientos_revertir_autorizacion[2];

        $this->assertTrue(
            $solicitud->estado == -3  &&
            count($solicitud->movimientos->where('id_tipo_movimiento',5))==1 &&
            $movimiento_autorizacion->movimiento_fondo_garantia->transaccion_generada->tipo_transaccion == 53 &&
            $movimiento_autorizacion->movimiento_fondo_garantia->transaccion_generada->estado == -2 &&
            $movimiento_autorizacion->movimiento_fondo_garantia->transaccion_generada->saldo == 0 &&
            $movimiento_autorizacion->movimiento_fondo_garantia->fondo_garantia->saldo == 100 &&
            $movimiento_revertir_autorizacion->movimiento_antecedente->id == $movimiento_autorizacion->id
        );
    }


    /**
     * Validar excepción por solicitudes pendientes de autorizar
     * @dataProvider datosSolicitudMovimientoLiberacionFG
     */

    public function testExcepcionGeneraSolicitudCuandoHaySolicitudesPendientes($datos)
    {
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_afectable_nuevo->id_subcontrato]);
        $this->expectExceptionMessage('Hay una solicitud de movimiento a fondo de garantía pendiente de autorizar, la solicitud actual no puede registrarse');
        SolicitudMovimientoFondoGarantia::create($datos);
        SolicitudMovimientoFondoGarantia::create($datos);
    }

    /**
     * Validar excepción por intentar registrar movimiento de registro de solicitud más de una vez
     * @dataProvider datosSolicitudMovimientoLiberacionFG
     */

    public function testExcepcionGenerarSolicitudConDosMovimientosDeRegistro($datos)
    {
        $this->generaNuevoFondo();
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_afectable_nuevo->id_subcontrato]);
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
     * @dataProvider datosSolicitudMovimientoLiberacionFG
     */

    public function testGeneraSolicitudMovimientoDeFondoDeGarantiaYSuMovimientoDeRegistro($datos)
    {
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_afectable_nuevo->id_subcontrato]);
        $solicitud = SolicitudMovimientoFondoGarantia::create($datos);

        $this->assertTrue($solicitud->id > 0 && count($solicitud->movimientos->where('id_tipo_movimiento',1))==1);

    }

    /**
     * @dataProvider datosSolicitudMovimientoLiberacionFG
     */
    public function testAutorizarSolicitudLiberacion($datos)
    {
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_afectable_nuevo->id_subcontrato]);
        $datos['observaciones'] = 'observaciones test autorización';
        $solicitud = SolicitudMovimientoFondoGarantia::create($datos);
        $solicitud->autorizar(["id_usuario"=>180]);
        $movimientos = $solicitud->movimientos->where('id_tipo_movimiento',2);

        $this->assertTrue(
            $solicitud->estado == 1  &&
            count($solicitud->movimientos->where('id_tipo_movimiento',2))==1 &&
            $movimientos[1]->movimiento_fondo_garantia->transaccion_generada->tipo_transaccion == 53 &&
            $movimientos[1]->movimiento_fondo_garantia->fondo_garantia->saldo == 50
        );
    }
    /**
     * @dataProvider datosSolicitudMovimientoDescuentoFG
     */
    public function testAutorizarSolicitudDescuento($datos)
    {
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_afectable_nuevo->id_subcontrato]);
        $datos['observaciones'] = 'observaciones test autorización';
        $solicitud = SolicitudMovimientoFondoGarantia::create($datos);
        $solicitud->autorizar(["id_usuario"=>180]);
        $movimientos = $solicitud->movimientos->where('id_tipo_movimiento',2);

        $this->assertTrue(
            $solicitud->estado == 1  &&
            count($solicitud->movimientos->where('id_tipo_movimiento',2))==1 &&
            $movimientos[1]->movimiento_fondo_garantia->transaccion_generada->tipo_transaccion == 53 &&
            $movimientos[1]->movimiento_fondo_garantia->fondo_garantia->saldo == 70
        );
    }

    /**
     * @dataProvider datosSolicitudMovimientoLiberacionFG
     */
    public function testCancelarSolicitudLiberacion($datos)
    {
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_afectable_nuevo->id_subcontrato]);
        $datos['observaciones'] = 'observaciones test cancelacion';
        $solicitud = SolicitudMovimientoFondoGarantia::create($datos);
        $solicitud->cancelar(["id_usuario"=>180, 'observaciones'=>"Error al registrar"]);
        $movimientos = $solicitud->movimientos->where('id_tipo_movimiento',3);

        $this->assertTrue(
            $solicitud->estado == -1  &&
            count($solicitud->movimientos->where('id_tipo_movimiento',3))==1
        );
    }
    /**
     * @dataProvider datosSolicitudMovimientoDescuentoFG
     */
    public function testCancelarSolicitudDescuento($datos)
    {
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_afectable_nuevo->id_subcontrato]);
        $datos['observaciones'] = 'observaciones test autorización';
        $solicitud = SolicitudMovimientoFondoGarantia::create($datos);
        $solicitud->cancelar(["id_usuario"=>180, 'observaciones'=>"Error al registrar solicitud de descuento"]);
        $movimientos = $solicitud->movimientos->where('id_tipo_movimiento',3);

        $this->assertTrue(
            $solicitud->estado == -1  &&
            count($solicitud->movimientos->where('id_tipo_movimiento',3))==1
        );
    }

    /**
     * @dataProvider datosSolicitudMovimientoLiberacionFG
     */
    public function testRechazarSolicitudLiberacion($datos)
    {
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_afectable_nuevo->id_subcontrato]);
        $datos['observaciones'] = 'observaciones test rechazar solictud de liberación';
        $solicitud = SolicitudMovimientoFondoGarantia::create($datos);
        $solicitud->rechazar(["id_usuario"=>182, 'observaciones'=>"Error en la solicitud"]);

        $this->assertTrue(
            $solicitud->estado == -2  &&
            count($solicitud->movimientos->where('id_tipo_movimiento',4))==1
        );
    }

    /**
     * @dataProvider datosSolicitudMovimientoDescuentoFG
     */
    public function testRechazarSolicitudDescuento($datos)
    {
        $datos = array_merge($datos,['id_fondo_garantia'=>$this->fondo_afectable_nuevo->id_subcontrato]);
        $datos['observaciones'] = 'observaciones test rechazar solicitud de descuento';
        $solicitud = SolicitudMovimientoFondoGarantia::create($datos);
        $solicitud->rechazar(["id_usuario"=>182, 'observaciones'=>"Error en la solicitud"]);

        $this->assertTrue(
            $solicitud->estado == -2  &&
            count($solicitud->movimientos->where('id_tipo_movimiento',4))==1
        );
    }


    /**
     * Data provider para registro de solicitud de liberación
     *
     */
    public function datosSolicitudMovimientoLiberacionFG()
    {

        return array(
            array(
                array(
                    'id_tipo_solicitud'=>1,
                    'fecha'=>'2019-02-21',
                    'referencia'=>'Referencia Liberación 21',
                    'importe'=>50,
                    'observaciones'=>'observaciones de prueba',
                    'usuario_registra'=>180
                )
            )
        );
    }
    /**
     * Data provider para registro de solicitud de descuento
     *
     */
    public function datosSolicitudMovimientoDescuentoFG()
    {

        return array(
            array(
                array(
                    'id_tipo_solicitud'=>2,
                    'fecha'=>'2019-02-21',
                    'referencia'=>'Referencia Descuento del 21',
                    'importe'=>30,
                    'observaciones'=>'observaciones de prueba',
                    'usuario_registra'=>180
                )
            )
        );
    }

    public function generaNuevoFondo(){
        /*$subcontrato_nuevo = Subcontrato::create([
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
            'id_obra' => 1,
            'observaciones'=>'OBSERVACIONES SUBCONTRATO 182',
            'id_usuario'=>182]);*/

        $subcontrato_nuevo = New Subcontrato();
        $subcontrato_nuevo->id_antecedente =208188;
        $subcontrato_nuevo->fecha = '2018-01-01';
        $subcontrato_nuevo->id_empresa = 100;
        $subcontrato_nuevo->id_moneda = 1;
        $subcontrato_nuevo->anticipo = 1;
        $subcontrato_nuevo->anticipo_monto = 100;
        $subcontrato_nuevo->anticipo_saldo = 100;
        $subcontrato_nuevo->monto = 1000;
        $subcontrato_nuevo->PorcentajeDescuento = 1;
        $subcontrato_nuevo->impuesto = 16;
        $subcontrato_nuevo->impuesto_retenido = 16;
        $subcontrato_nuevo->id_costo = 1;
        $subcontrato_nuevo->retencion = 10;
        $subcontrato_nuevo->referencia = 1;
        $subcontrato_nuevo->id_obra = 1;
        $subcontrato_nuevo->observaciones='OBSERVACIONES SUBCONTRATO 666';
        $subcontrato_nuevo->usuario_registra=182;
        $subcontrato_nuevo->save();

        $this->fondo_nuevo = $subcontrato_nuevo->fondo_garantia;
    }

    private function generaNuevoFondoAfectable()
    {
        /*$subcontrato_nuevo = Subcontrato::create([
            'id_antecedente' => 208188,
            'fecha' => '2018-01-01',
            'id_empresa' => 2,
            'id_moneda' => 1,
            'anticipo' => 1,
            'anticipo_monto' => 100,
            'anticipo_saldo' => 100,
            'monto' => 10000,
            'PorcentajeDescuento' => 1,
            'impuesto' => 16,
            'impuesto_retenido' => 16,
            'id_costo' => 1,
            'retencion' => 10,
            'referencia' => 1,
            'id_obra' => 1,
            'observaciones'=>'OBSERVACIONES SUBCONTRATO 181',
            'id_usuario'=>181]);*/

        $subcontrato_nuevo = New Subcontrato();
        $subcontrato_nuevo->id_antecedente =208188;
        $subcontrato_nuevo->fecha = '2018-01-01';
        $subcontrato_nuevo->id_empresa = 100;
        $subcontrato_nuevo->id_moneda = 1;
        $subcontrato_nuevo->anticipo = 1;
        $subcontrato_nuevo->anticipo_monto = 100;
        $subcontrato_nuevo->anticipo_saldo = 100;
        $subcontrato_nuevo->monto = 1000;
        $subcontrato_nuevo->PorcentajeDescuento = 1;
        $subcontrato_nuevo->impuesto = 16;
        $subcontrato_nuevo->impuesto_retenido = 16;
        $subcontrato_nuevo->id_costo = 1;
        $subcontrato_nuevo->retencion = 10;
        $subcontrato_nuevo->referencia = 1;
        $subcontrato_nuevo->id_obra = 1;
        $subcontrato_nuevo->observaciones='OBSERVACIONES SUBCONTRATO 181';
        $subcontrato_nuevo->usuario_registra=181;
        $subcontrato_nuevo->save();

        $estimacion_nueva = Estimacion::create(
            [
                'id_antecedente'=>$subcontrato_nuevo->id_transaccion,
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
            ]
        );
        $this->fondo_afectable_nuevo = $subcontrato_nuevo->fondo_garantia;
    }
}
