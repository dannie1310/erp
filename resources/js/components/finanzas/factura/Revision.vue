<template>
    <span>
        <div class="card" v-if="!Object.keys(factura).length > 0">
            <div class="card-body">
                <div >
                    <div class="row" >
                        <div class="col-md-12">
                            <div class="spinner-border text-success" role="status">
                               <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" v-if="Object.keys(factura).length > 0">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="invoice p-3 mb-3">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group error-content">
                                        <label >Empresa/Sucursal:</label>
                                        {{factura.empresa}}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <!--Referencia-->
                                    <div class="form-group error-content">
                                        <label>Fecha:</label>
                                        {{factura.fecha_format}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group error-content">
                                        <label >Referencia:</label>
                                        {{factura.referencia}}
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group error-content">
                                        <label >Contrarecibo:</label>
                                        {{factura.contra_recibo.numero_folio_format}}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <!--Referencia-->
                                    <div class="form-group error-content">
                                        <label >Vencimiento:</label>
                                        {{factura.vencimiento_format}}
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="col-md-12">
                                    <!--Referencia-->
                                    <div class="form-group error-content">
                                        <label for="observaciones">Observaciones:</label>
                                         <input class="form-control"
                                                style="width: 100%"
                                                placeholder="Observaciones"
                                                name="observaciones"
                                                id="observaciones"
                                                data-vv-as="Observaciones"
                                                v-validate="{required: true}"
                                                v-model="factura.observaciones"
                                                :class="{'is-invalid': errors.has('observaciones')}"
                                         >
                                        <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <Documento v-bind:id="id" v-bind:items="items" v-bind:id_moneda="factura.id_moneda" v-bind:cambios="tipo_cambio" @created="actualizar()"/>
                        <Conceptos v-bind:items="items" @created="actualizar()" /><br><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="bg-gray-light cantidad_input">Item Facturado</th>
                                    <th class="bg-gray-light cantidad_input">Unidad</th>
                                    <th class="bg-gray-light cantidad_input">Cantidad</th>
                                    <th class="bg-gray-light cantidad_input">Precio</th>
                                    <th class="bg-gray-light cantidad_input">Anticipo</th>
                                    <th class="bg-gray-light cantidad_input">Total</th>
                                    <th class="bg-gray-light cantidad_input">Tipo de Cambio</th>
                                    <th class="bg-gray-light cantidad_input">Referencia</th>
                                </tr>
                            </thead>
                            <tbody v-if="Object.keys(items).length > 0">

                                <tr v-for="item in items.pendientes" v-if="item.seleccionado">

                                        <td>{{item.insumo}}</td>
                                        <td>{{item.unidad}}</td>
                                        <td>{{item.cantidad}}</td>
                                        <td>${{item.precio}}</td>
                                        <td>$({{item.anticipo}})</td>
                                        <td>{{total_pendiente(item)}}</td>
                                        <td>{{getTipoCambioPendiente(item)}}</td>
                                        <td>{{item.remision}}</td>


                                </tr>
                                <tr v-for="item in items.subcontratos" v-if="item.seleccionado">
                                    <td colspan="5">{{item_subcontrato_desc(item)}}</td>
                                    <td>
                                        <input
                                            type="text"
                                            class="form-control"
                                            style="width: 100%; text-align: right"
                                            placeholder="Monto"
                                            name="monto_revision"
                                            id="monto_revision"
                                            data-vv-as="Monto Revision"
                                            v-validate="{required: true}"
                                            v-model="item.monto_revision"
                                            v-on:keyup="actualizar_subtotal()"
                                            :class="{'is-invalid': errors.has('monto_revision')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('monto_revision')">{{ errors.first('monto_revision') }}</div>
                                    </td>
                                    <td>{{getTipoCambioItem(item)}}</td>
                                    <td></td>
                                </tr>
                                <tr v-for="item in items.anticipos" v-if="item.seleccionado">
                                    <td colspan="5">{{item.descripcion_item}}</td>
                                    <td><input
                                        type="text"
                                        class="form-control"
                                        style="width: 100%; text-align: right"
                                        placeholder="Monto"
                                        name="monto_revision"
                                        id="monto_revision"
                                        data-vv-as="Monto Revision"
                                        v-validate="{required: true}"
                                        v-model="item.anticipo_sf"
                                        v-on:keyup="actualizar_subtotal()"
                                        :class="{'is-invalid': errors.has('monto_revision')}"
                                    >
                                        <div class="invalid-feedback" v-show="errors.has('monto_revision')">{{ errors.first('monto_revision') }}</div></td>
                                    <td>1</td>
                                    <td>{{item.transaccion}}</td>
                                </tr>
                                <tr v-for="item in items.renta" v-if="item.seleccionado">
                                    <td>{{item.equipo}}</td>
                                    <td>{{item.unidad}}</td>
                                    <td><input
                                        type="text"
                                        class="form-control"
                                        style="width: 100%; text-align: right"
                                        placeholder="Renta"
                                        name="renta"
                                        id="renta"
                                        data-vv-as="Renta"
                                        v-validate="{required: true}"
                                        v-model="item.rentas"
                                        v-on:keyup="actualizar_total_renta(item)"
                                        :class="{'is-invalid': errors.has('renta')}"
                                    >
                                        <div class="invalid-feedback" v-show="errors.has('renta')">{{ errors.first('renta') }}</div>
                                    </td>
                                    <td class="money">$ {{parseFloat(item.precio_unitario).formatMoney(2)}}</td>
                                    <td></td>
                                    <td class="money">{{getTotalRentaFormato(item.importe_total_rentas)}}</td>
                                    <td>{{item.id_moneda}}</td>
                                    <td></td>
                                </tr>
                                <tr v-for="item in items.lista" v-if="item.seleccionado">
                                    <td colspan="5">{{decode_utf8(item.referencia)}}</td>
                                    <td class="money" v-if="item.tipo_transaccion == 99">{{item.importe_total}}</td>
                                    <td v-else>
                                        <input
                                            type="text"
                                            class="form-control"
                                            style="width: 100%; text-align: right"
                                            placeholder="Monto"
                                            name="importe_total"
                                            id="importe_total"
                                            data-vv-as="Monto Revision"
                                            v-validate="{required: true}"
                                            v-model="item.importe_total_sf"
                                            v-on:keyup="actualizar_subtotal()"
                                            :class="{'is-invalid': errors.has('importe_total')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('importe_total')">{{ errors.first('importe_total') }}</div>
                                    </td>
                                    <td>{{item.id_moneda}}</td>
                                    <td>{{item.referencia_folio}}</td>
                                </tr>
                                <tr v-for="item in items.descuentos" v-if="item.seleccionado">
                                    <td colspan="5">{{decode_utf8(item.concepto)}}</td>
                                    <td>
                                        <input
                                            type="text"
                                            class="form-control"
                                            style="width: 100%; text-align: right"
                                            placeholder="Monto"
                                            name="monto_revision"
                                            id="monto_revision"
                                            data-vv-as="Monto Revision"
                                            v-validate="{required: true}"
                                            v-model="item.monto_revision"
                                            v-on:keyup="actualizar_subtotal()"
                                            :class="{'is-invalid': errors.has('monto_revision')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('monto_revision')">{{ errors.first('monto_revision') }}</div>
                                    </td>
                                    <td>1</td>
                                    <td></td>
                                </tr>
                                <tr v-for="item in items.conceptosEstimacion" v-if="item.seleccionado">
                                    <td colspan="5">{{item.descripcion_insumo}}</td>
                                    <td>
                                        <input
                                            type="text"
                                            class="form-control"
                                            style="width: 100%; text-align: right"
                                            placeholder="Monto"
                                            name="monto_revision"
                                            id="monto_revision"
                                            data-vv-as="Monto Revision"
                                            v-validate="{required: true}"
                                            v-model="item.monto_revision"
                                            v-on:keyup="actualizar_subtotal()"
                                            :class="{'is-invalid': errors.has('monto_revision')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('monto_revision')">{{ errors.first('monto_revision') }}</div>
                                    </td>
                                    <td>1</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-6">
                        <div class="form-group error-content">
                            <div class="row">
                                <div class="col-md-8" >
                                    Moneda:
                                </div>
                                <div class="col-md-3" style="text-align: right">
                                    {{factura.moneda}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <div class="row">
                                <div class="col-md-8" >
                                    Amortización de Anticipo:
                                </div>
                                <div class="col-md-4" style="text-align: right">
                                    ${{parseFloat(resumen.monto_anticipo_aplicado).formatMoney(2)}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-6 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <label for="tc_usd" class="col-md-8">Tipo Cambio USD:</label>
                                <input
                                    type="text"
                                    class="form-control col-md-3"
                                    style="width: 100%; text-align: right"
                                    placeholder="Tipo Cambio USD"
                                    name="tc_usd"
                                    id="tc_usd"
                                    data-vv-as="Tipo Cambio USD"
                                    v-validate="{required: true, decimal:4}"
                                    v-model="tipo_cambio[2]"
                                    v-on:keyup="actualizar_resumen()"
                                    :class="{'is-invalid': errors.has('tc_usd')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('tc_usd')">{{ errors.first('tc_usd') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <div class="row">
                                <div class="col-md-8" >
                                    Total Deductivas:
                                </div>
                                <div class="col-md-4" style="text-align: right">
                                    ${{parseFloat(resumen.total_deductivas_estimacion).formatMoney(2)}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-6 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <label for="tc_eur" class="col-md-8">Tipo Cambio EUR:</label>
                                <input
                                    type="text"
                                    class="form-control col-md-3"
                                    style="width: 100%; text-align: right"
                                    placeholder="Tipo Cambio EUR"
                                    name="tc_eur"
                                    id="tc_eur"
                                    data-vv-as="Tipo Cambio EUR"
                                    v-validate="{required: true, decimal:4}"
                                    v-model="tipo_cambio[3]"
                                    v-on:keyup="actualizar_resumen()"
                                    :class="{'is-invalid': errors.has('tc_eur')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('tc_eur')">{{ errors.first('tc_eur') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <div class="row">
                                <div class="col-md-8" >
                                    Subtotal:
                                </div>
                                <div class="col-md-4" style="text-align: right">
                                    <b>
                                        ${{parseFloat(resumen.subtotal).formatMoney(2)}}
                                    </b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-md-9">
                        <div class="form-group error-content">
                            <div class="row">
                                <div class="col-md-8" >
                                    Fondo de Garantia:
                                </div>
                                <div class="col-md-4" style="text-align: right">
                                    ${{parseFloat(resumen.fondo_garantia).formatMoney(2)}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-6 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <div class="col-md-7" >
                                    Importe de Factura:
                                </div>
                                <div class="col-md-4" style="text-align: right">
                                    <b>
                                        {{factura.monto_format}}
                                    </b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <div class="row">
                                <label class="col-md-8" for="iva_subtotal">IVA Subtotal:</label>
                                <input
                                    type="text"
                                    class="form-control col-md-4"
                                    style="width: 100%; text-align: right"
                                    placeholder="IVA Subtotal"
                                    name="iva_subtotal"
                                    id="iva_subtotal"
                                    data-vv-as="IVA Subtotal"
                                    v-validate="{required: true, regex: /^[0-9]\d*(\.\d+)?$/}"
                                    v-model="resumen.iva_subtotal"
                                    v-on:keyup="actualizar_resumen()"
                                    :class="{'is-invalid': errors.has('iva_subtotal')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('iva_subtotal')">{{ errors.first('iva_subtotal') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-6 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <div class="col-md-7" >
                                    Total de Revisión:
                                </div>
                                <div class="col-md-4" style="text-align: right">
                                    <b>${{parseFloat(resumen.total_documentos).formatMoney(2)}}</b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <div class="row">
                                <label class="col-md-8" for="ret_iva_4">Retención IVA (4%):</label>
                                <input
                                    type="text"
                                    class="form-control col-md-4"
                                    style="width: 100%; text-align: right"
                                    placeholder="Ret IVA (4%)"
                                    name="ret_iva_4"
                                    id="ret_iva_4"
                                    data-vv-as="Ret IVA (4%)"
                                    v-validate="{required: true, regex: /^[0-9]\d*(\.\d+)?$/}"
                                    v-model="resumen.ret_iva_4"
                                    v-on:keyup="actualizar_resumen()"
                                    :class="{'is-invalid': errors.has('ret_iva_4')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('ret_iva_4')">{{ errors.first('ret_iva_4') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-6">
                        <hr style="margin: 0px 0px 1em 0px">
                        <div class="form-group error-content">
                            <div class="row">
                                <div class="col-md-7" >
                                    Diferencia:
                                </div>
                                <div class="col-md-4" style="text-align: right" :style="diferencia_sf()<0?`color : #F00`:``">
                                    <b>${{diferencia()}}</b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <div class="row">
                                <label class="col-md-8" for="ret_iva_6">Retención IVA (6%):</label>
                                <input
                                    type="text"
                                    class="form-control col-md-4"
                                    style="width: 100%; text-align: right"
                                    placeholder="Ret IVA (6%)"
                                    name="ret_iva_6"
                                    id="ret_iva_6"
                                    data-vv-as="Ret IVA (10%)"
                                    v-validate="{required: true, regex: /^[0-9]\d*(\.\d+)?$/}"
                                    v-model="resumen.ret_iva_6"
                                    v-on:keyup="actualizar_resumen()"
                                    :class="{'is-invalid': errors.has('ret_iva_6')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('ret_iva_6')">{{ errors.first('ret_iva_6') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-9 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <label class="col-md-8" for="ret_iva_23">Retención IVA (2/3):</label>
                                <input
                                    type="text"
                                    class="form-control col-md-4"
                                    style="width: 100%; text-align: right"
                                    placeholder="Ret IVA (2/3)"
                                    name="ret_iva_23"
                                    id="ret_iva_23"
                                    data-vv-as="Ret IVA (10%)"
                                    v-validate="{required: true, regex: /^[0-9]\d*(\.\d+)?$/}"
                                    v-model="resumen.ret_iva_23"
                                    v-on:keyup="actualizar_resumen()"
                                    :class="{'is-invalid': errors.has('ret_iva_23')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('ret_iva_23')">{{ errors.first('ret_iva_23') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-9 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <div class="col-md-8" >
                                    IVA A Pagar:
                                </div>
                                <div class="col-md-4" style="text-align: right">
                                    ${{parseFloat(resumen.iva_pagar).formatMoney(2)}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-9 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <label class="col-md-8" for="ieps">IEPS:</label>
                                <input
                                    type="text"
                                    class="form-control col-md-4"
                                    style="width: 100%; text-align: right"
                                    placeholder="IEPS"
                                    name="ieps"
                                    id="ieps"
                                    data-vv-as="IEPS"
                                    v-validate="{required: true, regex: /^[0-9]\d*(\.\d+)?$/}"
                                    v-model="resumen.ieps"
                                    v-on:keyup="actualizar_resumen()"
                                    :class="{'is-invalid': errors.has('ieps')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('ieps')">{{ errors.first('ieps') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-9 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <label class="col-md-8" for="imp_hospedaje">Impuesto a Hospedaje:</label>
                                <input
                                    type="text"
                                    class="form-control col-md-4"
                                    style="width: 100%; text-align: right"
                                    placeholder="Impuesto a Hospedaje"
                                    name="imp_hospedaje"
                                    id="imp_hospedaje"
                                    data-vv-as="Impuesto a Hospedaje"
                                    v-validate="{required: true, regex: /^[0-9]\d*(\.\d+)?$/}"
                                    v-model="resumen.imp_hospedaje"
                                    v-on:keyup="actualizar_resumen()"
                                    :class="{'is-invalid': errors.has('imp_hospedaje')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('imp_hospedaje')">{{ errors.first('imp_hospedaje') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-9 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <label class="col-md-8" for="ret_isr_125">Retención a ISR (1.25%):</label>
                                <input
                                    type="text"
                                    class="form-control col-md-4"
                                    style="width: 100%; text-align: right"
                                    placeholder="Retención a ISR (10%)"
                                    name="ret_isr_125"
                                    id="ret_isr_125"
                                    data-vv-as="Retención a ISR (1.25%)"
                                    v-validate="{required: true, regex: /^[0-9]\d*(\.\d+)?$/}"
                                    v-model="resumen.ret_isr_125"
                                    v-on:keyup="actualizar_resumen()"
                                    :class="{'is-invalid': errors.has('ret_isr_125')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('ret_isr_125')">{{ errors.first('ret_isr_125') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-9 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <label class="col-md-8" for="ret_isr_10">Retención a ISR (10%):</label>
                                <input
                                    type="text"
                                    class="form-control col-md-4"
                                    style="width: 100%; text-align: right"
                                    placeholder="Retención a ISR (10%)"
                                    name="ret_isr_10"
                                    id="ret_isr_10"
                                    data-vv-as="Retención a ISR (10%)"
                                    v-validate="{required: true, regex: /^[0-9]\d*(\.\d+)?$/}"
                                    v-model="resumen.ret_isr_10"
                                    v-on:keyup="actualizar_resumen()"
                                    :class="{'is-invalid': errors.has('ret_isr_10')}"
                                >
                                <div class="invalid-feedback" v-show="errors.has('ret_isr_10')">{{ errors.first('ret_isr_10') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-9 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <div class="col-md-8" >
                                    Retenciones Subcontratos:
                                </div>
                                <div class="col-md-4" style="text-align: right">
                                    ${{parseFloat(resumen.ret_subcontratos).formatMoney(2)}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-9 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <div class="col-md-8" >
                                    Devolución Retenciones Subcontratos:
                                </div>
                                <div class="col-md-4" style="text-align: right">
                                    ${{parseFloat(resumen.dev_ret_subcontratos).formatMoney(2)}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 offset-9 ">
                        <div class="form-group error-content">
                            <div class="row">
                                <div class="col-md-8" >
                                    <b>Total de revisión:</b>
                                </div>
                                <div class="col-md-4" style="text-align: right">
                                    <b>${{parseFloat(resumen.total_documentos).formatMoney(2)}}</b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span class="pull-right">
                    <button type="button" class="btn btn-secondary" v-on:click="salir">
                    <i class="fa fa-angle-left"></i> Regresar
                    </button>
                    <button type="button" class="btn btn-primary" v-on:click="validate"><i class="fa fa-check"></i> Revisar</button>
                </span>
            </div>
        </div>
    </span>
</template>

<script>
import Documento from './Documentos';
import Conceptos from './ConceptosEstimacion';
export default {
    name: "factura-revisar",
    components: {Documento, Conceptos},
    props: ['id'],

    data() {
        return {
            configuracion:[],
            factura: [],
            resumen:{
                subtotal:0,
                fondo_garantia:0,
                iva_subtotal:0,
                ret_iva_4:0,
                ret_iva_6:0,
                ret_iva_23:0,
                iva_pagar:0,
                ieps:0,
                imp_hospedaje:0,
                ret_isr_10:0,
                ret_isr_125:0,
                ret_subcontratos:0,
                dev_ret_subcontratos:0,
                total_deductivas_estimacion:0,
                monto_anticipo_aplicado:0,
                total_documentos:0,
            },
            items:[],
            tipo_cambio:[]

        }
    },
    mounted() {
        this.getDocumentos();
        this.$Progress.start();
        },
    methods: {

        actualizar(){
            this.resumen.subtotal = parseFloat(0);
            this.resumen.fondo_garantia = parseFloat(0);
            this.resumen.iva_subtotal = parseFloat(0);
            this.resumen.ret_iva_4 = parseFloat(0);
            this.resumen.ret_iva_6 = parseFloat(0);
            this.resumen.ret_iva_23 = parseFloat(0);
            this.resumen.iva_pagar = parseFloat(0);
            this.resumen.ieps = parseFloat(0);
            this.resumen.imp_hospedaje = parseFloat(0);
            this.resumen.ret_isr_10 = parseFloat(0);
            this.resumen.ret_isr_125 = parseFloat(0);
            this.resumen.ret_subcontratos = parseFloat(0);
            this.resumen.dev_ret_subcontratos = parseFloat(0);
            this.resumen.total_deductivas_estimacion = parseFloat(0);
            this.resumen.monto_anticipo_aplicado = parseFloat(0);
            this.resumen.total_documentos = parseFloat(0);
            if(this.items){
                this.items.pendientes.forEach(pendiente => {
                    if(pendiente.seleccionado){
                        if(pendiente.id_moneda == this.factura.id_moneda){
                            this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(((pendiente.cantidad * pendiente.precio_sf) - pendiente.anticipo));
                        }else{
                            this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(((pendiente.cantidad * pendiente.precio_sf) - pendiente.anticipo) * this.tipo_cambio[pendiente.id_moneda]);
                        }
                    }

                });
                this.items.anticipos.forEach(anticipo => {
                    if(anticipo.seleccionado){
                        if(anticipo.id_moneda == this.factura.id_moneda){
                            this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(anticipo.anticipo_sf);
                        }else{
                            this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(anticipo.anticipo_sf / this.tipo_cambio[this.factura.id_moneda]);
                        }
                    }
                });
                this.items.subcontratos.forEach(subcontrato => {
                    let t_cambio = 1;
                    if(subcontrato.id_moneda != this.factura.id_moneda){
                        t_cambio = this.tipo_cambio[subcontrato.id_moneda];
                    }
                    if(subcontrato.seleccionado){
                        if(!subcontrato.tc_actualizado){
                            subcontrato.monto_revision = parseFloat(subcontrato.monto_revision * t_cambio);
                            subcontrato.tc_actualizado = true;
                        }
                        if(parseInt(subcontrato.tipo_transaccion) == 51){
                            this.resumen.subtotal = parseFloat(this.resumen.subtotal) + parseFloat(subcontrato.monto_revision);
                            // this.resumen.iva_subtotal = parseFloat(this.resumen.iva_subtotal) + parseFloat(subcontrato.monto_revision * 0.16);
                        }
                        if(parseInt( subcontrato.tipo_transaccion) == 52){
                            this.resumen.subtotal = parseFloat(this.resumen.subtotal) + parseFloat(subcontrato.monto_revision);
                            this.resumen.fondo_garantia = parseFloat(this.resumen.fondo_garantia) + parseFloat(subcontrato.retencion_fondo_garantia_sf);
                            // this.resumen.iva_subtotal = parseFloat(this.resumen.iva_subtotal) + parseFloat(subcontrato.impuesto);
                            this.resumen.ret_iva_4 = parseFloat(this.resumen.ret_iva_4) + parseFloat(subcontrato.retencion_iva4);
                            this.resumen.ret_iva_6 = parseFloat(this.resumen.ret_iva_6) + parseFloat(subcontrato.retencion_iva6);
                            this.resumen.ret_iva_23 = parseFloat(this.resumen.ret_iva_23) + parseFloat(subcontrato.retencion_iva23);
                            this.resumen.ret_subcontratos = parseFloat(this.resumen.ret_subcontratos) + parseFloat(subcontrato.suma_penalizaciones_sf);
                            this.resumen.dev_ret_subcontratos = parseFloat(this.resumen.dev_ret_subcontratos) + parseFloat(subcontrato.suma_penalizaciones_liberadas_sf);
                            this.resumen.total_deductivas_estimacion = parseFloat(this.resumen.total_deductivas_estimacion) + parseFloat(subcontrato.total_deductivas_sf);
                            this.resumen.monto_anticipo_aplicado = parseFloat(this.resumen.monto_anticipo_aplicado) + parseFloat(subcontrato.monto_anticipo_aplicado);
                            if(subcontrato.porcentaje_isr_retenido == 1.25){
                                this.resumen.ret_isr_125 = parseFloat(this.resumen.ret_isr_125) + parseFloat(subcontrato.monto_isr_retenido);
                            }
                            if(subcontrato.porcentaje_isr_retenido == 10){
                                this.resumen.ret_isr_10 = parseFloat(this.resumen.ret_isr_10) + parseFloat(subcontrato.monto_isr_retenido);
                            }
                        }
                        if(parseInt(subcontrato.tipo_transaccion) == 53){
                            this.resumen.subtotal = parseFloat(this.resumen.subtotal) + parseFloat(subcontrato.monto_revision);
                        }
                    }

                });
                this.items.renta.forEach(rent => {
                    if(rent.seleccionado){
                        this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(rent.importe_total_rentas / this.tipo_cambio[this.factura.id_moneda]);
                    }
                });
                this.items.lista.forEach(list => {
                    if(list.seleccionado){
                        this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(list.importe_total_sf / this.tipo_cambio[this.factura.id_moneda]);
                    }
                });


                if(this.configuracion.ret_fon_gar_antes_iva == 1){
                    this.resumen.subtotal = this.resumen.subtotal -  this.resumen.fondo_garantia;
                }
                if(this.configuracion.penalizacion_antes_iva == 1){
                    this.resumen.subtotal = parseFloat(this.resumen.subtotal) - (this.resumen.ret_subcontratos) + (this.resumen.dev_ret_subcontratos);
                }

                if(this.configuracion.desc_pres_mat_antes_iva == 1){
                    this.resumen.subtotal = (this.resumen.subtotal) - (this.resumen.total_deductivas_estimacion);
                }

                let otros_impuestos = parseFloat(this.resumen.imp_hospedaje) + parseFloat(this.resumen.ieps);
                let retenciones = parseFloat(this.resumen.ret_iva_4) + parseFloat(this.resumen.ret_iva_6) + parseFloat(this.resumen.ret_isr_10);
                this.resumen.subtotal = (this.resumen.subtotal) - (this.resumen.monto_anticipo_aplicado);

                this.resumen.iva_subtotal = parseFloat(this.resumen.subtotal * 0.16);
                this.resumen.iva_pagar =  parseFloat(this.resumen.iva_subtotal) - parseFloat(this.resumen.ret_iva_23);

                if(this.configuracion.ret_fon_gar_antes_iva == 0 && this.configuracion.ret_fon_gar_con_iva == 0){
                    this.resumen.subtotal = this.resumen.subtotal -  this.resumen.fondo_garantia;
                }
                if(this.configuracion.penalizacion_antes_iva == 0){
                    this.resumen.subtotal = this.resumen.subtotal - this.resumen.ret_subcontratos + this.resumen.dev_ret_subcontratos;
                }
                if(this.configuracion.desc_pres_mat_antes_iva == 0){
                    this.resumen.subtotal = this.resumen.subtotal - this.resumen.total_deductivas_estimacion;
                }

                this.resumen.total_documentos = this.resumen.subtotal + this.resumen.iva_pagar + otros_impuestos - retenciones;
                this.actualizar_subtotal_descuentos();
                this.actualizar_resumen();
            }
        },
        actualizar_subtotal_descuentos(){
            this.items.descuentos.forEach(descuento => {
                    if(descuento.seleccionado){
                        if(descuento.naturaleza === 'Descuento'){
                            this.resumen.subtotal = parseFloat(this.resumen.subtotal) -  (descuento.monto_revision);
                        }
                        if(descuento.naturaleza === 'Recargo'){
                            this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(descuento.monto_revision);
                        }

                    }
                });
                this.items.conceptosEstimacion.forEach(conceptoEst => {
                    if(conceptoEst.seleccionado){
                        if(conceptoEst.mascara === 8192){
                            this.resumen.subtotal = parseFloat(this.resumen.subtotal) -  (conceptoEst.monto_revision);
                        }
                        if(conceptoEst.mascara === 12288){
                            this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(conceptoEst.monto_revision);
                        }

                    }
                });
        },
        actualizar_subtotal(){
            this.resumen.subtotal = 0;
            this.items.pendientes.forEach(pendiente => {
                if(pendiente.seleccionado){
                    this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat((pendiente.cantidad * pendiente.precio_sf) - pendiente.anticipo);
                }

            });
            this.items.anticipos.forEach(anticipo => {
                if(anticipo.seleccionado){
                    this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(anticipo.anticipo_sf);
                }
            });
            this.items.subcontratos.forEach(subcontrato => {
                if(subcontrato.seleccionado){
                    if(parseInt(subcontrato.tipo_transaccion) == 51){
                        this.resumen.subtotal = parseFloat(this.resumen.subtotal) + parseFloat(subcontrato.monto_revision);
                        // this.resumen.iva_subtotal = parseFloat(this.resumen.iva_subtotal) + parseFloat(subcontrato.monto_revision * 0.16);
                    }
                    if(parseInt( subcontrato.tipo_transaccion) == 52){
                        this.resumen.subtotal = parseFloat(this.resumen.subtotal) + parseFloat(subcontrato.monto_revision);
                    }
                    if(parseInt(subcontrato.tipo_transaccion) == 53){
                        this.resumen.subtotal = parseFloat(this.resumen.subtotal) + parseFloat(subcontrato.monto_revision);
                    }
                }

            });
            this.items.renta.forEach(rent => {
                if(rent.seleccionado){
                    this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(rent.importe_total_rentas);
                }
            });
            this.items.lista.forEach(list => {
                if(list.seleccionado){
                    this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(list.importe_total_sf);
                }
            });

            if(this.configuracion.ret_fon_gar_antes_iva == 1){
                this.resumen.subtotal = this.resumen.subtotal -  this.resumen.fondo_garantia;
            }
            if(this.configuracion.penalizacion_antes_iva == 1){
                this.resumen.subtotal = parseFloat(this.resumen.subtotal) - (this.resumen.ret_subcontratos) + (this.resumen.dev_ret_subcontratos);
            }

            if(this.configuracion.desc_pres_mat_antes_iva == 1){
                this.resumen.subtotal = (this.resumen.subtotal) - (this.resumen.total_deductivas_estimacion);
            }

            let otros_impuestos = parseFloat(this.resumen.imp_hospedaje) + parseFloat(this.resumen.ieps);
            let retenciones = parseFloat(this.resumen.ret_iva_4) + parseFloat(this.resumen.ret_iva_6) + parseFloat(this.resumen.ret_isr_10);
            this.resumen.subtotal = (this.resumen.subtotal) - (this.resumen.monto_anticipo_aplicado);

            //this.resumen.iva_subtotal = parseFloat(this.resumen.subtotal * 0.16);
            this.resumen.iva_pagar =  parseFloat(this.resumen.iva_subtotal) - parseFloat(this.resumen.ret_iva_23);

            if(this.configuracion.ret_fon_gar_antes_iva == 0 && this.configuracion.ret_fon_gar_con_iva == 0){
                this.resumen.total_documentos = this.resumen.total_documentos -  this.resumen.fondo_garantia;
            }
            if(this.configuracion.penalizacion_antes_iva == 0){
                this.resumen.total_documentos = this.resumen.total_documentos - this.resumen.ret_subcontratos + this.resumen.dev_ret_subcontratos;
            }
            if(this.configuracion.desc_pres_mat_antes_iva == 0){
                this.resumen.total_documentos = this.resumen.total_documentos - this.resumen.total_deductivas_estimacion;
            }

            this.resumen.total_documentos = this.resumen.subtotal + this.resumen.iva_pagar + otros_impuestos - retenciones;
            this.actualizar_subtotal_descuentos();
            this.actualizar_resumen();
        },
        actualizar_resumen(){
            this.resumen.iva_pagar =  parseFloat(this.resumen.iva_subtotal) - parseFloat(this.resumen.ret_iva_23);
            let otros_impuestos =  parseFloat(this.resumen.imp_hospedaje)  +  parseFloat(this.resumen.ieps);
            let retenciones = parseFloat(this.resumen.ret_iva_4) + parseFloat(this.resumen.ret_iva_6) + parseFloat(this.resumen.ret_isr_10) + parseFloat(this.resumen.ret_isr_125);
            this.resumen.total_documentos = parseFloat(this.resumen.subtotal) + this.resumen.iva_pagar + otros_impuestos - retenciones;
        },
        find(){
            return this.$store.dispatch('finanzas/factura/find', {
                id: this.id,
                params: { include: 'contra_recibo' }
            })
            .then(data => {
                this.factura = data;
                this.setTipoCambio(data.tipo_cambio_fecha);
            }).finally(()=>{
                this.$Progress.finish();
            })
        },
        getConfiguracionObra(){
            return this.$store.dispatch('finanzas/estimacion/index', {
                    params: { scope: 'porObra' } 
                })
                .then(data => {
                    this.configuracion = data.data[0]
                }).finally(()=>{
                    this.find();
                });
        },
        getDocumentos(){
            if(Object.keys(this.items).length === 0){
                this.cargando = true;
                return this.$store.dispatch('finanzas/factura/getDocumentos', {
                    id: this.id,
                    // params: { include: 'contra_recibo' }
                })
                .then(data => {
                    // this.$store.commit('finanzas/factura/SET_ITEMS_REVISION', data);
                    this.items = data;
                    this.cargando = false;
                }).finally(()=>{
                    this.getConfiguracionObra();
                })
            }

        },
        format_money(){
            this.resumen.monto_anticipo_aplicado = parseFloat(this.resumen.monto_anticipo_aplicado).toFixed(2);
            this.resumen.total_deductivas_estimacion = parseFloat(this.resumen.total_deductivas_estimacion).toFixed(2);
            this.resumen.subtotal = parseFloat(this.resumen.subtotal).toFixed(2);
            this.resumen.iva_subtotal = parseFloat(this.resumen.iva_subtotal).toFixed(2);
            this.resumen.iva_pagar = parseFloat(this.resumen.iva_pagar).toFixed(2);
            this.resumen.fondo_garantia = parseFloat(this.resumen.fondo_garantia).toFixed(2);
            this.resumen.total_documentos = parseFloat(this.resumen.total_documentos).toFixed(2);
        },
        diferencia(){
            return parseFloat(this.resumen.total_documentos - this.factura.monto).formatMoney(2);
        },
        diferencia_sf(){
            return this.resumen.total_documentos - this.factura.monto;
        },
        item_subcontrato_desc(item){
            if(item.tipo_transaccion == 51){
                return 'Anticipo de Contrato  ';
            }
            if(item.tipo_transaccion == 52){
                return item.folio_revision_format + '[Periodo ' + item.fecha_inicial + ' - ' + item.fecha_final + ']';
            }
            if(item.tipo_transaccion == 53){
                return item.folio_revision_format ;
            }
            return '';
        },
        total_pendiente(item){
            return '$' + parseFloat((item.cantidad * item.precio_sf) - item.anticipo).formatMoney(2,'.',',');
        },
        setTipoCambio(tipos){
            tipos.forEach(tipo =>{
                this.tipo_cambio[tipo.id_moneda] = parseFloat(tipo.cambio);
            });
            this.tipo_cambio[1] = 1;
        },
        store(){
            let items = this.filtrar_items_seleccionados();
            return this.$store.dispatch('finanzas/factura/storeRevision', {
                factura:this.factura,
                items:items,
                resumen:this.resumen,
                tipo_cambio:this.tipo_cambio,
                diferencia:parseFloat(this.resumen.total_documentos - this.factura.monto),
            })
                .then((data) => {
                    this.$router.push({name: 'factura'});
                });

        },
        validate() {
            this.$validator.validate().then(result => {
                if (result){
                    if(Math.abs(parseFloat(this.resumen.total_documentos - this.factura.monto)) > 0.99){
                        swal('¡Error!', 'Para proceder con la revisión, la diferencia debe ser menor a 0.99', 'error')
                    }else{
                        this.store()
                    }

                }
            });
        },
        filtrar_items_seleccionados(){
            let resp = {
                'pendientes':[],
                'anticipos':[],
                'subcontratos':[],
                'renta':[],
                'lista':[],
                'descuentos':[],
                'conceptos':[]
            };
            this.items.pendientes.forEach(pendiente => {
                if(pendiente.seleccionado){
                    resp.pendientes.push(pendiente);
                }

            });
            this.items.anticipos.forEach(anticipo => {
                if(anticipo.seleccionado){
                    resp.anticipos.push(anticipo);
                }
            });
            this.items.subcontratos.forEach(subcontrato => {
                if(subcontrato.seleccionado){
                    resp.subcontratos.push(subcontrato);
                }

            });
            this.items.renta.forEach(rent => {
                if(rent.seleccionado){
                    resp.renta.push(rent);
                }
            });
            this.items.lista.forEach(list => {
                if(list.seleccionado){
                    resp.lista.push(list);
                }
            });
            this.items.descuentos.forEach(descuento => {
                if(descuento.seleccionado){
                    resp.descuentos.push(descuento);
                }
            });
            this.items.conceptosEstimacion.forEach(concepto => {
                if(concepto.seleccionado){
                    resp.conceptos.push(concepto);
                }
            });
            return resp;
        },
        salir(){
            this.$router.push({name: 'factura'});
        },
        decode_utf8(s) {
            return decodeURIComponent(escape(s));
        },
        actualizar_total_renta(item){
            item.importe_total_rentas = parseFloat(item.rentas * item.precio_unitario);
            this.actualizar_subtotal();
        },
        getTotalRentaFormato(importe){
            return '$ ' + parseFloat(importe).formatMoney(2);
        },
        getTipoCambioItem(item){
            if(item.tipo_transaccion == 52){
                return parseFloat(item.monto_revision / item.subtotal).toFixed(6)
            }else if(item.tipo_transaccion == 51){
                return parseFloat(item.monto_revision / item.anticipo_monto).toFixed(6)
            }
            return parseFloat(item.tipo_cambio).toFixed(6);
        },
        getTipoCambioPendiente(item){
            if(item.id_moneda == this.factura.id_moneda){
                return 1;
            }else{
                return this.tipo_cambio[item.id_moneda];
            }
        }
    },
    computed:{
        total(){


            return 0;
        },
        // items(){
        //     return this.$store.getters['finanzas/factura/items_revision'];
        // },
    },

}
</script>

<style>

</style>
