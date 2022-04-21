<template>
    <span>
        <div v-if="cargando">
            <div class="row" >
                <div class="col-md-12">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="invoice p-3 mb-3">
                <div class="modal-body" v-if="pago">
                    <div class="row">
                        <div class="col-md-12">
                            <span><i class="fa fa-dollar-sign"></i>Datos del Pago</span>
                            <div class="table-responsive">
                                <table class="table  table-sm">
                                    <tr>
                                        <td colspan="6" class="encabezado centrado">
                                            {{pago.empresa.razon_social}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="encabezado" >
                                            Pago
                                        </th>
                                        <th class="encabezado">
                                            Referencia
                                        </th>
                                        <th class="encabezado">
                                            Fecha
                                        </th>
                                        <th class="encabezado">
                                            Monto
                                        </th>
                                        <th class="encabezado">
                                            Saldo
                                        </th>
                                        <th class="encabezado">
                                            Moneda
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{pago.numero_folio_format}}
                                        </td>
                                        <td>
                                            {{pago.referencia}}
                                        </td>
                                        <td>
                                            {{pago.fecha_format}}
                                        </td>
                                        <td style="text-align: right">
                                            {{pago.monto_positivo_format}}
                                        </td>
                                        <td style="text-align: right">
                                            {{pago.saldo_format}}
                                        </td>
                                        <td>
                                            {{pago.moneda.nombre}}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group row error-content">
                                <label for="factura" class="col-sm-2 col-form-label">Aplicar a: </label>
                                <div class="col-sm-10">
                                    <model-list-select
                                            id="factura"
                                            :disabled="cargando"
                                            name="factura"
                                            v-validate="{required: true}"
                                            v-model="factura"
                                            option-value="id"
                                            :custom-text="referenciaNumeroFolio"
                                            :list="facturas"
                                            :placeholder="!cargando?'Seleccionar o buscar la transacción factura por referencia o folio':'Cargando...'"
                                            >
                                    </model-list-select>
                                    <div class="invalid-feedback" v-show="errors.has('factura')">{{ errors.first('factura') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><b>Fecha: </b></label>
                                <span v-if="partidas">{{facturas[index_factura].fecha_format}}</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><b>Moneda: </b></label>
                                <span v-if="partidas">{{facturas[index_factura].moneda}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="th_c150">Saldo</th>
                                            <th class="th_c150">Aplicar</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="partidas">
                                        <tr v-for="(fac, i) in partidas" v-if="fac.saldo_format != 0">
                                            <td>{{fac.descripcion_antecedente}}</td>
                                            <td class="td_money">${{fac.saldo_format}}</td>
                                            <td>
                                                <input
                                                    type="text"
                                                    v-on:keyup="getSubtotal()"
                                                    style="text-align: right; padding: 3px"
                                                    :name="`aplicar[${i}]`"
                                                    data-vv-as="Aplicar"
                                                    v-validate="{required: true, max_value:fac.saldo_base, regex: /^[0-9]\d*(\.\d+)?$/}"
                                                    class="form-control"
                                                    id="aplicar"
                                                    v-model="fac.saldo"
                                                    :class="{'is-invalid': errors.has(`aplicar[${i}]`)}">
                                                <div class="invalid-feedback" v-show="errors.has(`aplicar[${i}]`)">{{ errors.first(`aplicar[${i}]`) }}</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><b>Saldo: </b></label>
                                <input
                                    style="text-align: right"
                                    type="text"
                                    id="saldo"
                                    name="saldo"
                                    v-model="pago.saldo_format"
                                    disabled="true">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="moneda">Moneda:</label>
                                <input
                                    style="text-align: center"
                                    type="text"
                                    id="moneda"
                                    name="moneda"
                                    v-model="pago.moneda.nombre"
                                    disabled="true">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipo_cambio"><b>T.C.:</b></label>
                                <input
                                    v-on:keyup="recalculaTotal()"
                                    style="text-align: right"
                                    type="text"
                                    id="tipo_cambio"
                                    name="tipo_cambio"
                                    v-model="tipo_cambio_factura"
                                    :disabled="id_moneda_factura == 1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label for="subtotal"  class="col-sm-5 col-form-label"><b>Subtotal:</b></label>
                                <div class="col-md-7">
                                    <input
                                        style="width: 100%; text-align: right"
                                        type="text"
                                        id="subtotal"
                                        name="subtotal"
                                        disabled="true"
                                        v-model="subtotal">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 offset-9">
                            <div class="form-group row">
                                <label for="impuesto" class="col-sm-5 col-form-label"><b>IVA:</b></label>
                                <div class="col-md-7">
                                    <input
                                        v-on:keyup="recalculaTotal()"
                                        style="width: 100%; text-align: right; padding: 3px"
                                        type="text"
                                        id="impuesto"
                                        name="impuesto"
                                        v-validate="{regex: /^[0-9]\d*(\.\d+)?$/}"
                                        class="form-control"
                                        :class="{'is-invalid': errors.has('impuesto')}"
                                        v-model="iva"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 offset-9">
                            <div class="form-group row">
                                <label for="impuesto" class="col-sm-5 col-form-label"><b>Impuestos Retenidos:</b></label>
                                <div class="col-md-7">
                                    <input
                                        disabled="true"
                                        style="width: 100%; text-align: right; padding: 3px"
                                        type="text"
                                        id="impuesto_retenido"
                                        name="impuesto_retenido"
                                        v-model="impuesto_retenido_format"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 offset-9">
                            <div class="form-group row">
                                <label for="total" class="col-sm-5 col-form-label"><b>Total:</b></label>
                                <div class="col-md-7">
                                    <input
                                        style="width: 100%; text-align: right"
                                        type="text"
                                        id="total"
                                        name="total"
                                        disabled="true"
                                        v-model="total">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 offset-9">
                            <div class="form-group row">
                                <label for="aplicado" class="col-sm-5 col-form-label"><b>Aplicado:</b></label>
                                <div class="col-md-7">
                                    <input
                                        style="width: 100%; text-align: right"
                                        type="text"
                                        id="aplicado"
                                        name="aplicado"
                                        disabled="true"
                                        v-model="aplicado">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <label for="observaciones">Observaciones:</label>
                                <textarea
                                        name="observaciones"
                                        id="observaciones"
                                        class="form-control"
                                        v-model="observaciones"
                                        v-validate="{required: true}"
                                        data-vv-as="Observaciones"
                                        :class="{'is-invalid': errors.has('observaciones')}"
                                ></textarea>
                                <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary pull-right" @click="regresar()"><i class="fa fa-angle-left"></i>Regresar</button>
                    <button type="button" class="btn btn-primary pull-right" @click="validate" :disabled="validaPartidas()"><i class="fa fa-save"></i>Aplicar</button>
                </div>

            </div>
        </div>

    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
export default {
    name: "pago-pendiente-aplicar",
    props: ['id'],
    components: {ModelListSelect},
    data() {
        return {
            cargando:false,
            facturas:null,
            factura:'',
            index_factura:null,
            observaciones:'',
            partidas:null,
            pago:null,
            subtotal:'',
            iva:'',
            total:'',
            aplicado:'',
            impuesto_retenido: '',
            impuesto_retenido_format: '',
            guardando:false,
            cambioPago: null,
            tipo_cambio_factura: 1,
            id_moneda_factura:1

        }
    },
    mounted() {
        this.find();
    },
    methods: {
        regresar(){
            swal({
                title: "Salir de Aplicación de Pagos Pendientes",
                text: "¿Está seguro de que quiere salir del registro de pagos pendientes?",
                icon: "info",
                buttons: {
                    cancel: {
                        text: 'Cancelar',
                        visible: true
                    },
                    confirm: {
                        text: 'Si, Salir',
                        closeModal: true,
                    }
                }
            })
            .then((value) => {
                if (value) {
                    this.$router.push({name: 'pago-manual'});
                }
            });
        },
        find(){
            this.cargando=true;
            return this.$store.dispatch('finanzas/pago/find', {
                id:this.id,
                params: {include: ['moneda', 'empresa', 'cambioFecha'], scope:'pendientePorAplicar'}
                }).then(data=>{
                    this.pago = data;
                    this.cambioPago = data.cambioFecha.data;
                    this.findFacturas(data.id_empresa);
                })
                .finally(()=>{
                })

        },
        referenciaNumeroFolio(item)
        {
            return `[${item.numero_folio_format}] - [ ${item.referencia} ]`;
        },
        findFacturas(id_empresa){
            return this.$store.dispatch('finanzas/factura/facturasAplicacionManual', {
                id:id_empresa,
                params: {
                    scope: 'pendientePago',
                    include:['partidas']
                }
            }).then(data=>{
                this.facturas = data.data;
            })
            .finally(()=>{
                this.cargando=false;
            })
        },
        getSubtotal(){
            let sbt = 0;
            if(this.partidas){
                this.partidas.forEach(partida => {
                    sbt = sbt + parseFloat(partida.saldo);
                });
            }
            this.subtotal = parseFloat(sbt);
            this.iva = parseFloat(sbt * 0.16);
            this.total = parseFloat(this.subtotal + this.iva - this.impuesto_retenido).formatMoney(2,'.','');
            this.aplicado = parseFloat((this.total * this.tipo_cambio_factura)).formatMoney(2,'.',',');
            this.subtotal = parseFloat(this.subtotal).formatMoney(2,'.',',');
            this.iva = parseFloat(this.iva).formatMoney(2,'.',',');
            this.total = parseFloat(this.total).formatMoney(2,'.',',');
            
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result){
                    if((this.pago.saldo * -1) < this.aplicado){
                    swal('¡Error!', 'El total a aplicar es mayor al saldo del pago.', 'error')
                    }else{
                        this.store();
                    }
                }
            });
        },
        store(){
            this.guardando = true;
            return this.$store.dispatch('finanzas/pago/aplicarPago', {
                id:this.id,
                partidas:this.partidas,
                id_factura:this.factura,
                observaciones:this.observaciones,
                subtotal:this.getCantidadFloat(this.subtotal),
                impuesto: this.getCantidadFloat(this.iva),
                monto: this.getCantidadFloat(this.total),
                aplicado: this.getCantidadFloat(this.aplicado),
                tipo_cambio: this.tipo_cambio_factura,
            }).then(data=>{
                this.$router.push({name: 'pago-manual'});
            })
            .finally(()=>{
                this.guardando=false;
            })

        },
        validaPartidas(){
            if(!this.partidas){
                return true;
            }
            let subtotal = 0;
            this.partidas.forEach(partida => {
                subtotal = subtotal + parseFloat(partida.saldo);
            });
            return subtotal == 0;
        },
        recalculaTotal(){
            let total = parseFloat(this.subtotal) + parseFloat(this.iva);
            this.total = total.formatMoney(2,'.','')
            this.aplicado = parseFloat(total * this.tipo_cambio_factura).formatMoney(2,'.','');
        },
        getCantidadFloat(cantidad){
            return parseFloat(cantidad.replace(",", "")).formatMoney(2,'.','');
        },
    },
    watch:{
        factura(value){
            if(value != ''){
                let idx_cambioFecha = 0;
                let idx = this.facturas.findIndex(f => f.id == value);
                this.partidas = this.facturas[idx].partidas.data;
                this.index_factura = this.facturas.findIndex(f => f.id == value);
                if(this.facturas[this.index_factura]['id_moneda'] != 1){
                    idx_cambioFecha = this.cambioPago.findIndex(cp => cp.id == this.facturas[this.index_factura]['id_moneda']);
                    this.tipo_cambio_factura = parseFloat(this.cambioPago[idx_cambioFecha]['cambio']).formatMoney(4,'.','');
                    this.id_moneda_factura = this.facturas[this.index_factura]['id_moneda'];
                }
                this.impuesto_retenido = this.facturas[this.index_factura]['impuesto_retenido'];
                this.impuesto_retenido_format = this.facturas[this.index_factura]['impuesto_retenido_format'];
                this.getSubtotal();
            }else{
                this.partidas = null;
            }
        }
    }
}
</script>

<style scoped>
    .encabezado{
        text-align: center; background-color: #f2f4f5
    }
    td, th{
        border: 1px #dee2e6 solid;
    }
</style>
