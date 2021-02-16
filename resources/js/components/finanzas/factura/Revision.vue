<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row" v-if="Object.keys(factura).length > 0">
                        <div class="col-md-10">
                            <div class="form-group error-content">
                                <label for="fecha">Empresa/Sucursal:</label>
                                {{factura.empresa}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <!--Referencia-->
                            <div class="form-group error-content">
                                <label for="referencia">Fecha:</label>
                                {{factura.fecha_format}}
                            </div>
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-md-5">
                            <div class="form-group error-content">
                                <label for="fecha">Referencia:</label>
                                {{factura.referencia}}
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group error-content">
                                <label for="fecha">Contrarecibo:</label>
                                {{factura.contra_recibo.numero_folio_format}}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <!--Referencia-->
                            <div class="form-group error-content">
                                <label for="referencia">Vencimiento:</label>
                                {{factura.vencimiento_format}}
                            </div>
                        </div>
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
            <div class="col-12" v-if="Object.keys(factura).length > 0">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <Documento v-bind:id="id" v-bind:items="items" @created="actualizar()"/><br><br>
                        </div>
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="bg-gray-light">Item Facturado</th>
                                        <th class="bg-gray-light">Unidad</th>
                                        <th class="bg-gray-light">Cantidad</th>
                                        <th class="bg-gray-light">Precio</th>
                                        <th class="bg-gray-light">Anticipo</th>
                                        <th class="bg-gray-light">Total</th>
                                        <th class="bg-gray-light">T/C</th>
                                        <th class="bg-gray-light">Referencia</th>
                                    </tr>
                                </thead>
                                <tbody v-if="Object.keys(items).length > 0">
                                    
                                    <tr v-for="item in items.pendientes" v-if="item.seleccionado">
                                        
                                            <td>{{item.insumo}}</td>
                                            <td>{{item.unidad}}</td>
                                            <td>{{item.cantidad}}</td>
                                            <td>$ {{item.precio}}</td>
                                            <td>$({{item.anticipo}})</td>
                                            <td>{{total_pendiente(item)}}</td>
                                            <td>$ {{item.id_moneda}}</td>
                                            <td> {{item.remision}}</td>
                                        
                                        
                                    </tr>
                                    <tr v-for="item in items.subcontratos" v-if="item.seleccionado">
                                        <td colspan="5">{{item_subcontrato_desc(item)}}</td>
                                        <td>
                                            <input 
                                                type="number"
                                                step=".01" 
                                                class="form-control"
                                                style="width: 100%"
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
                                        <td>{{item.tipo_cambio}}</td>
                                        <td></td>
                                    </tr>
                                    <tr v-for="item in items.anticipos" v-if="item.seleccionado">
                                        <td colspan="5">{{item.descripcion_item}}</td>
                                        <td>$ {{item.anticipo}}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr v-for="item in items.renta" v-if="item.seleccionado">
                                        <td colspan="4">{{item.equipo}} - {{item.numero_serie}}</td>
                                        <td>{{item.unidad}}</td>
                                        <td>{{item.importe_total}}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr v-for="item in items.descuentos" v-if="item.seleccionado">
                                        <td colspan="5">{{decode_utf8(item.concepto)}}</td>
                                        <td>
                                            <input 
                                                type="number"
                                                step=".01"
                                                class="form-control"
                                                style="width: 100%"
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
                        <div class="col-md-3 offset-6 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Moneda:</label>
                                    Pesos
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Amortización de Anticipo:</label>
                                    {{resumen.monto_anticipo_aplicado}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 offset-6 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="tc_usd" class="col-md-5">Tipo Cambio USD:</label>
                                    <input 
                                        type="number"
                                        step=".01" 
                                        class="form-control col-md-6"
                                        style="width: 100%"
                                        placeholder="Tipo Cambio USD"
                                        name="tc_usd"
                                        id="tc_usd"
                                        data-vv-as="Tipo Cambio USD"
                                        v-validate="{required: true}"
                                        v-model="tipo_cambio.usd"
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
                                    <label for="referencia" class="col-md-5">Total Deductivas:</label>
                                    {{resumen.total_deductivas_estimacion}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 offset-6 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="tc_eur" class="col-md-5">Tipo Cambio EUR:</label>
                                    <input 
                                        type="number"
                                        step=".01" 
                                        class="form-control col-md-6"
                                           style="width: 100%"
                                           placeholder="Tipo Cambio EUR"
                                           name="tc_eur"
                                           id="tc_eur"
                                           data-vv-as="Tipo Cambio EUR"
                                           v-validate="{required: true}"
                                           v-model="tipo_cambio.eur"
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
                                    <label for="referencia" class="col-md-5">Subtotal:</label>
                                    {{resumen.subtotal}}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 offset-6 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Diferencia:</label>
                                    {{diferencia()}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Fondo de Garantia:</label>
                                    {{resumen.fondo_garantia}}
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-3 offset-6 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Documento Original:</label>
                                    {{factura.monto_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">IVA Subtotal:</label>
                                    <input 
                                        type="number"
                                        step=".01" 
                                        class="form-control col-md-6"
                                           style="width: 100%"
                                           placeholder="IVA Subtotal"
                                           name="iva_subtotal"
                                           id="iva_subtotal"
                                           data-vv-as="IVA Subtotal"
                                           v-validate="{required: true}"
                                           v-model="resumen.iva_subtotal"
                                           v-on:keyup="actualizar_resumen()"
                                           :class="{'is-invalid': errors.has('iva_subtotal')}"
                                        >
                                    <div class="invalid-feedback" v-show="errors.has('iva_subtotal')">{{ errors.first('iva_subtotal') }}</div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-md-3 offset-9">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Ret IVA (4%):</label>
                                    <input 
                                        type="number"
                                        step=".01" 
                                        class="form-control col-md-6"
                                           style="width: 100%"
                                           placeholder="Ret IVA (4%)"
                                           name="ret_iva_4"
                                           id="ret_iva_4"
                                           data-vv-as="Ret IVA (4%)"
                                           v-validate="{required: true}"
                                           v-model="resumen.ret_iva_4"
                                           v-on:keyup="actualizar_resumen()"
                                           :class="{'is-invalid': errors.has('ret_iva_4')}"
                                        >
                                    <div class="invalid-feedback" v-show="errors.has('ret_iva_4')">{{ errors.first('ret_iva_4') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 offset-9">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Ret IVA (6%):</label>
                                    <input 
                                        type="number"
                                        step=".01" 
                                        class="form-control col-md-6"
                                           style="width: 100%"
                                           placeholder="Ret IVA (6%)"
                                           name="ret_iva_6"
                                           id="ret_iva_6"
                                           data-vv-as="Ret IVA (10%)"
                                           v-validate="{required: true}"
                                           v-model="resumen.ret_iva_6"
                                           v-on:keyup="actualizar_resumen()"
                                           :class="{'is-invalid': errors.has('ret_iva_6')}"
                                        >
                                    <div class="invalid-feedback" v-show="errors.has('ret_iva_6')">{{ errors.first('ret_iva_6') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 offset-9 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Ret IVA (2/3):</label>
                                    <input 
                                        type="number"
                                        step=".01" 
                                        class="form-control col-md-6"
                                           style="width: 100%"
                                           placeholder="Ret IVA (2/3%)"
                                           name="ret_iva_23"
                                           id="ret_iva_23"
                                           data-vv-as="Ret IVA (10%)"
                                           v-validate="{required: true}"
                                           v-model="resumen.ret_iva_23"
                                           v-on:keyup="actualizar_resumen()"
                                           :class="{'is-invalid': errors.has('ret_iva_23')}"
                                        >
                                    <div class="invalid-feedback" v-show="errors.has('ret_iva_23')">{{ errors.first('ret_iva_23') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 offset-9 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">IVA A Pagar:</label>
                                    {{resumen.iva_pagar}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 offset-9 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">IEPS:</label>
                                    <input 
                                        type="number"
                                        step=".01" 
                                        class="form-control col-md-6"
                                           style="width: 100%"
                                           placeholder="IEPS"
                                           name="ieps"
                                           id="ieps"
                                           data-vv-as="IEPS"
                                           v-validate="{required: true}"
                                           v-model="resumen.ieps"
                                           v-on:keyup="actualizar_resumen()"
                                           :class="{'is-invalid': errors.has('ieps')}"
                                        >
                                    <div class="invalid-feedback" v-show="errors.has('ieps')">{{ errors.first('ieps') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 offset-9 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Imp Hospedaje:</label>
                                    <input 
                                        type="number"
                                        step=".01" 
                                        class="form-control col-md-6"
                                           style="width: 100%"
                                           placeholder="Imp Hospedaje"
                                           name="imp_hospedaje"
                                           id="imp_hospedaje"
                                           data-vv-as="Imp Hospedaje"
                                           v-validate="{required: true}"
                                           v-model="resumen.imp_hospedaje"
                                           v-on:keyup="actualizar_resumen()"
                                           :class="{'is-invalid': errors.has('imp_hospedaje')}"
                                        >
                                    <div class="invalid-feedback" v-show="errors.has('imp_hospedaje')">{{ errors.first('imp_hospedaje') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 offset-9 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Ret. ISR (10%):</label>
                                    <input 
                                        type="number"
                                        step=".01" 
                                        class="form-control col-md-6"
                                           style="width: 100%"
                                           placeholder="Ret ISR (10%)"
                                           name="ret_isr_10"
                                           id="ret_isr_10"
                                           data-vv-as="Ret ISR (10%)"
                                           v-validate="{required: true}"
                                           v-model="resumen.ret_isr_10"
                                           v-on:keyup="actualizar_resumen()"
                                           :class="{'is-invalid': errors.has('ret_isr_10')}"
                                        >
                                    <div class="invalid-feedback" v-show="errors.has('ret_isr_10')">{{ errors.first('ret_isr_10') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 offset-9 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Retenciones Subcontratos:</label>
                                    {{resumen.ret_subcontratos}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 offset-9 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Devolución Retenciones Subcontratos:</label>
                                    {{resumen.dev_ret_subcontratos}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 offset-9 ">
                            <div class="form-group error-content">
                                <div class="row">
                                    <label for="referencia" class="col-md-5">Total:</label>
                                    {{resumen.total_documentos}}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
            <div class="col-md-12" v-if="Object.keys(factura).length > 0">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary float-right" v-on:click="validate">Aceptar</button>
                            <button type="button" class="btn btn-default float-right" style="margin-right:5px" v-on:click="salir">Cerrar</button>
                        </div>
                        
                    </div>
                </div>
                        
            </div>
        </div>
    </span>
</template>

<script>
import Documento from './Documentos'
export default {
    name: "factura-revisar",
    components: {Documento},
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
                ret_subcontratos:0,
                dev_ret_subcontratos:0,
                total_deductivas_estimacion:0,
                monto_anticipo_aplicado:0,
                total_documentos:0,
            },
            items:[],
            tipo_cambio:{
                usd:0,
                eur:0,
            },
        }
    },
    mounted() {
        this.getDocumentos();
        this.getConfiguracionObra();
        this.$Progress.start();
        this.find()
            .finally(() => {
                this.$Progress.finish();
            })
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
            this.resumen.ret_subcontratos = parseFloat(0);
            this.resumen.dev_ret_subcontratos = parseFloat(0);
            this.resumen.total_deductivas_estimacion = parseFloat(0);
            this.resumen.monto_anticipo_aplicado = parseFloat(0);
            this.resumen.total_documentos = parseFloat(0);
            if(this.items){
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
                            this.resumen.fondo_garantia = parseFloat(this.resumen.fondo_garantia) + parseFloat(subcontrato.retencion_fondo_garantia_sf);
                            // this.resumen.iva_subtotal = parseFloat(this.resumen.iva_subtotal) + parseFloat(subcontrato.impuesto);
                            this.resumen.ret_iva_4 = parseFloat(this.resumen.ret_iva_4) + parseFloat(subcontrato.retencion_iva4);
                            this.resumen.ret_iva_6 = parseFloat(this.resumen.ret_iva_6) + parseFloat(subcontrato.retencion_iva6);
                            this.resumen.ret_iva_23 = parseFloat(this.resumen.ret_iva_23) + parseFloat(subcontrato.retencion_iva23);
                            this.resumen.ret_subcontratos = parseFloat(this.resumen.ret_subcontratos) + parseFloat(subcontrato.suma_penalizaciones_sf);
                            this.resumen.dev_ret_subcontratos = parseFloat(this.resumen.dev_ret_subcontratos) + parseFloat(subcontrato.suma_penalizaciones_liberadas_sf);
                            this.resumen.total_deductivas_estimacion = parseFloat(this.resumen.total_deductivas_estimacion) + parseFloat(subcontrato.total_deductivas_sf);
                            this.resumen.monto_anticipo_aplicado = parseFloat(this.resumen.monto_anticipo_aplicado) + parseFloat(subcontrato.monto_anticipo_aplicado);
                        }
                        if(parseInt(subcontrato.tipo_transaccion) == 53){
                            this.resumen.subtotal = parseFloat(this.resumen.subtotal) + parseFloat(subcontrato.monto_revision);
                        }
                    }
                    
                });
                this.items.renta.forEach(rent => {
                    if(rent.seleccionado){
                        this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(anticipo.anticipo_sf);
                    }
                });
                this.items.lista.forEach(list => {
                    if(list.seleccionado){
                        this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(list.importe_total_sf);
                    }
                });
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
                
                if(this.configuracion.ret_fon_gar_antes_iva == 1){
                    this.resumen.subtotal = this.resumen.subtotal -  this.resumen.fondo_garantia; 
                }
                if(this.configuracion.penalizacion_antes_iva == 1){
                    this.resumen.subtotal = parseFloat(this.resumen.subtotal) - (this.resumen.ret_subcontratos) + (this.resumen.dev_ret_subcontratos);
                }
                
                if(this.configuracion.desc_pres_mat_antes_iva == 1){
                    this.resumen.subtotal = (this.resumen.subtotal) - (this.resumen.total_deductivas_estimacion);
                }
                
                let otros_impuestos =  parseFloat(this.resumen.imp_hospedaje) + parseFloat(this.resumen.ieps) + parseFloat(this.resumen.ret_isr_10);
                let retenciones = parseFloat(this.resumen.ret_iva_4) + parseFloat(this.resumen.ret_iva_6);
                this.resumen.subtotal = (this.resumen.subtotal) - (this.resumen.monto_anticipo_aplicado);
                
                this.resumen.iva_subtotal = parseFloat(this.resumen.subtotal * 0.16);
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

                this.resumen.total_documentos = this.resumen.subtotal + this.resumen.iva_pagar - otros_impuestos - retenciones;
                this.format_money();
            }
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
                    this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(anticipo.anticipo_sf);
                }
            });
            this.items.lista.forEach(list => {
                if(list.seleccionado){
                    this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(list.importe_total_sf);
                }
            });
            this.items.descuentos.forEach(descuento => {
                if(descuento.seleccionado){
                    descuento.monto_revision = descuento.monto_revision === '' ? 0 : (descuento.monto_revision);
                    if(descuento.naturaleza === 'Descuento'){
                        this.resumen.subtotal = parseFloat(this.resumen.subtotal) -  parseFloat(descuento.monto_revision);
                    }
                    if(descuento.naturaleza === 'Recargo'){
                        this.resumen.subtotal = parseFloat(this.resumen.subtotal) +  parseFloat(descuento.monto_revision);
                    }
                    
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
            
            let otros_impuestos =  parseFloat(this.resumen.imp_hospedaje) + parseFloat(this.resumen.ieps) + parseFloat(this.resumen.ret_isr_10);
            let retenciones = parseFloat(this.resumen.ret_iva_4) + parseFloat(this.resumen.ret_iva_6);
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

            this.resumen.total_documentos = this.resumen.subtotal + this.resumen.iva_pagar - otros_impuestos - retenciones;
            this.actualizar_resumen();
        },
        actualizar_resumen(){
            this.resumen.iva_subtotal = this.resumen.iva_subtotal === ''?0:parseFloat(this.resumen.iva_subtotal);
            this.resumen.ret_iva_4 = this.resumen.ret_iva_4 === ''?0:parseFloat(this.resumen.ret_iva_4);
            this.resumen.ret_iva_6 = this.resumen.ret_iva_6 === ''?0:parseFloat(this.resumen.ret_iva_6);
            this.resumen.ret_iva_23 = this.resumen.ret_iva_23 === ''?0:parseFloat(this.resumen.ret_iva_23);
            this.resumen.ieps = this.resumen.ieps === ''?0:parseFloat(this.resumen.ieps);
            this.resumen.imp_hospedaje = this.resumen.imp_hospedaje === ''?0:parseFloat(this.resumen.imp_hospedaje);
            this.resumen.ret_isr_10 = this.resumen.ret_isr_10 === ''?0:parseFloat(this.resumen.ret_isr_10);
            this.resumen.iva_subtotal = this.resumen.iva_subtotal === ''?0:parseFloat(this.resumen.iva_subtotal);

            this.resumen.iva_pagar =  parseFloat(this.resumen.iva_subtotal - this.resumen.ret_iva_23);
            let otros_impuestos =  parseFloat(this.resumen.imp_hospedaje  +  this.resumen.ieps  +  this.resumen.ret_isr_10);
            let retenciones = parseFloat(this.resumen.ret_iva_4 + this.resumen.ret_iva_6);
            console.log(this.resumen.total_documentos);
            this.resumen.total_documentos = parseFloat(this.resumen.subtotal) + this.resumen.iva_pagar - otros_impuestos - retenciones;
            this.format_money();
        },
        find(){
            return this.$store.dispatch('finanzas/factura/find', {
                id: this.id,
                params: { include: 'contra_recibo' }
            })
            .then(data => {
                this.factura = data;
                this.setTipoCambio(data.tipo_cambio_fecha);
            })
        },
        getConfiguracionObra(){
            return this.$store.dispatch('finanzas/estimacion/index', { } )
                .then(data => {
                    this.configuracion = data.data[0]
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
                if(tipo.id_moneda ==2){
                    this.tipo_cambio.usd = parseFloat(tipo.cambio);
                }
                if(tipo.id_moneda ==3){
                    this.tipo_cambio.eur = parseFloat(tipo.cambio);
                }
            });
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
                'descuentos':[]
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
            return resp;
        },
        salir(){
            this.$router.push({name: 'factura'});
        },
        decode_utf8(s) {
            return decodeURIComponent(escape(s));
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
