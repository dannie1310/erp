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
                            <span><i class="fa fa-envelope"></i>Datos de Pago</span>
                            <div class="table-responsive">
                                <table class="table  table-sm">
                                    <tr>
                                        <td colspan="3" class="encabezado centrado">
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
                                            :disabled="cargando"
                                            name="factura"
                                            v-validate="{required: true}"
                                            v-model="factura"
                                            option-value="id"
                                            option-text="referencia"
                                            :list="facturas"
                                            :placeholder="!cargando?'Seleccionar o buscar factura por referencia':'Cargando...'"
                                            >
                                    </model-list-select>
                                    <div class="invalid-feedback" v-show="errors.has('factura')">{{ errors.first('factura') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><b>Fecha: </b></label>
                                <span v-if="partidas"><b>{{facturas[index_factura].fecha_format}}</b></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><b>Moneda: </b></label>
                                <span v-if="partidas"><b>{{facturas[index_factura].moneda}}</b></span>
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
                                        <tr v-for="(fac, i) in partidas" v-if="fac.saldo > 0">
                                            <td>{{fac.descripcion_antecedente}}</td>
                                            <td class="td_money">${{fac.saldo_format}}</td>
                                            <td>
                                                <input
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    v-on:keyup="getSubtotal()"
                                                    style="text-align: right"
                                                    type="text"
                                                    name="aplicar"
                                                    data-vv-as="Aplicar"
                                                    v-validate="{required: true, min:0.01}"
                                                    class="form-control"
                                                    id="aplicar"
                                                    v-model="fac.saldo_format"
                                                    :class="{'is-invalid': errors.has('aplicar')}">
                                                <div class="invalid-feedback" v-show="errors.has('aplicar')">{{ errors.first('aplicar') }}</div>
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
                                <label id="tipo_cambio"><b>Tipo de Cambio</b></label>
                                <input 
                                    style="text-align: right"
                                    type="text"
                                    id="tipo_cambio"
                                    name="tipo_cambio"
                                    v-model="pago.moneda.tipo_cambio"
                                    disabled="true">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label id="subtotal"  class="col-sm-5 col-form-label"><b>Subtotal</b></label>
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
                                <label id="impuesto" class="col-sm-5 col-form-label"><b>IVA</b></label>
                                <div class="col-md-7">
                                    <input 
                                        style="width: 100%; text-align: right"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        type="text"
                                        id="impuesto"
                                        name="impuesto"
                                        v-model="iva">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 offset-9">
                            <div class="form-group row">
                                <label id="total" class="col-sm-5 col-form-label"><b>Total</b></label>
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
                                <label id="aplicado" class="col-sm-5 col-form-label"><b>Aplicado</b></label>
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
                    <button type="button" class="btn btn-secondary pull-right" @click="cerrar()"><i class="fa fa-close"></i>Cerrar</button>
                    <button type="button" class="btn btn-primary pull-right" @click="validate"><i class="fa fa-save"></i>Aplicar</button>
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
            guardando:false,

        }
    },
    mounted() {
        this.find();
    },
    methods: {
        cerrar(){
        },
        find(){
            this.cargando=true;
            return this.$store.dispatch('finanzas/pago/find', {
                id:this.id,
                params: {include: ['moneda', 'empresa'], scope:'OrdenPago'}
                }).then(data=>{
                    this.pago = data;
                    this.findFacturas(data.id_empresa);
                })
                .finally(()=>{
                })

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
                    sbt = sbt + parseFloat(partida.importe);
                });
            }
            this.subtotal = sbt
            this.iva = sbt * 0.16;
            this.total = this.subtotal + this.iva;
            this.aplicado = this.total * this.facturas[this.index_factura]['tipo_cambio'];
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result){
                    this.store();
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
                subtotal:this.subtotal,
                impuesto: this.iva,
                monto: this.total,
                aplicado: this.aplicado
            }).then(data=>{
                this.$router.push({name: 'pago-manual'});
            })
            .finally(()=>{
                this.guardando=false;
            })

        }
    },
    watch:{
        factura(value){
            if(value != ''){
                let idx = this.facturas.findIndex(f => f.id == value);
                this.partidas = this.facturas[idx].partidas.data;
                this.index_factura = this.facturas.findIndex(f => f.id == value);
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