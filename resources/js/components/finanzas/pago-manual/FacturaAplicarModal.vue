<template>
    <span>
        <button @click="findFacturas()" type="button" class="btn btn-sm btn-outline-info" title="Aplicar Pago">
            <i class="fa fa-check"></i>
            <!-- <i class="fa fa-spinner fa-spin" v-else></i> -->
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-check"></i> FACTURAS PENDIENTES APLICACIÓN PAGO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row" v-if="cargando">
                            <div class="col-md-12">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div v-else-if="facturas">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><b>Pago: </b></label>
                                        <b>{{pago.numero_folio}}</b>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><b>Referencia: </b></label>
                                        <b>{{pago.referencia}}</b>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><b>Razón Social: </b></label>
                                        <b>{{pago.razon_social}}</b>
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
                                        <span v-if="index_factura != ''"><b>{{facturas[index_factura].fecha_format}}</b></span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><b>Moneda: </b></label>
                                        <span v-if="index_factura != ''"><b>{{facturas[index_factura].moneda}}</b></span>
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
                                                <tr v-for="(fac, i) in partidas">
                                                    <td>{{fac.descripcion_antecedente}}</td>
                                                    <td class="td_money">${{fac.saldo_format}}</td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            name="aplicar"
                                                            data-vv-as="Aplicar"
                                                            v-validate="{required: true}"
                                                            class="form-control"
                                                            id="aplicar"
                                                            v-model="fac.saldo"
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
                                            type="text"
                                            id="saldo"
                                            name="saldo"
                                            disabled="true">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="moneda">Moneda:</label>
                                        <input 
                                            type="text"
                                            id="moneda"
                                            name="moneda"
                                            disabled="true">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label id="tipo_cambio"><b>Tipo de Cambio</b></label>
                                        <input 
                                            type="text"
                                            id="tipo_cambio"
                                            name="tipo_cambio"
                                            disabled="true">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label id="subtotal"  class="col-sm-5 col-form-label"><b>Subtotal</b></label>
                                        <div class="col-md-7">
                                            <input 
                                            type="text"
                                            id="subtotal"
                                            name="subtotal"
                                            disabled="true"
                                            class="td_money">
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
                                                type="text"
                                                id="impuesto"
                                                name="impuesto"
                                                class="td_money">
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
                                                type="text"
                                                id="total"
                                                name="total"
                                                disabled="true"
                                                class="td_money">
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
                                                type="text"
                                                id="aplicado"
                                                name="aplicado"
                                                disabled="true"
                                                class="td_money">
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
                    </div>
                    <div class="modal footer">
                        <button type="button" class="btn btn-secondary" @click="cerrar()"><i class="fa fa-close"></i>Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="validate"><i class="fa fa-save"></i>Aplicar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
export default {
    name: "mano-obra-editar",
    props: ['pago','id_empresa'],
    components: {ModelListSelect},
    data() {
        return {
            cargando:false,
            facturas:null,
            factura:'',
            index_factura:'',
            observaciones:'',
            partidas:null
        }
    },
    methods: {
        cerrar(){
            $(this.$refs.modal).modal('hide');
        },
        findFacturas(){
            this.cargando = true
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
            return this.$store.dispatch('finanzas/factura/facturasAplicacionManual', {
                id:this.id_empresa,
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
        validate() {
            this.$validator.validate().then(result => {
                if (result){
                    this.store();
                }
            });
        },
        store(){
            console.log('panda');
        }
    },
    watch:{
        factura(value){
            if(value != ''){
                let idx = this.facturas.findIndex(f => f.id == value);
                this.partidas = this.facturas[idx].partidas.data;
                this.index_factura = this.facturas.findIndex(f => f.id == value);
            }else{
                this.partidas = null;
            }
        }
    }
}
</script>

<style>

</style>