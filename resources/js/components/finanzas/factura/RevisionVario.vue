<template>
    <span>
        <div v-if="factura == ''">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <i class="fa fa-spin fa-spinner"></i>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h5>Datos de Contrarecibo</h5>
                        <br>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group float-right">
                            <label><b>Fecha: </b></label>
                            Fecha Select
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><b>Contrarecibo: </b></label>
                            {{factura.contra_recibo.numero_folio_format}}
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label><b>Naturaleza: </b></label>
                            select
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label><b>Fecha: </b></label>
                            {{factura.cumplimiento}}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label><b>Vencimiento: </b></label>
                            {{factura.vencimiento}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{factura.referencia}}
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            {{factura.empresa}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><b>Concepto: </b>&nbsp</label> <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(-1)" ></i>
                            <span v-if="concepto != ''">{{concepto.descripcion}}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div  class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:35%;">Concepto</th>
                                        <th style="width:12px;">Cantidad</th>
                                        <th style="width:12px;">Precio</th>
                                        <th style="width:12px;">Monto</th>
                                        <th style="width:19%;">Destino</th>
                                        <th style="width:8%;"></th>
                                        <th style="width:2%;">
                                            <button type="button" class="btn btn-success btn-sm" v-if="cargando"  title="Cargando..." :disabled="cargando">
                                                <i class="fa fa-spin fa-spinner"></i>
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm"  @click="addPartidas()" v-else>
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr  v-for="(partida, i) in partidas">
                                        <td>
                                            <select
                                                    type="text"
                                                    name="concepto"
                                                    data-vv-as="Concepto"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="concepto"
                                                    v-model="partida.concepto"
                                                    :class="{'is-invalid': errors.has('concepto')}"
                                                >
                                                        <option value>-- SELECCIONE --</option>
                                                        <option v-for="concepto in conceptos" :value="concepto">{{ concepto }}</option>
                                                        <optgroup label="Servicios">Servicios</optgroup>
                                                        <option v-for="servicio in servicios" :value="servicio">{{ servicio }}</option>

                                                </select>
                                                <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                                        </td>
                                        <td>
                                            <input type="number"
                                                    min="0.01"
                                                    step=".01"
                                                    class="form-control"
                                                    :name="`cantidad[${i}]`"
                                                    data-vv-as="Cantidad"
                                                    v-validate="{required: true}"
                                                    :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                    v-model="partida.cantidad"/>
                                            <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                        </td>
                                        <td>
                                            <input type="number"
                                                    min="0.01"
                                                    step=".01"
                                                    class="form-control"
                                                    :name="`precio[${i}]`"
                                                    data-vv-as="Precio"
                                                    v-validate="{required: true}"
                                                    :class="{'is-invalid': errors.has(`precio[${i}]`)}"
                                                    v-model="partida.precio"/>
                                            <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                        </td>
                                        <td>{{getMonto(partida)}}</td>
                                        <td v-if="partida.destino ==''"></td>
                                        <td v-else style="text-decoration: underline"  :title="partida.destino.path">{{partida.destino.descripcion}}</td>
                                        <td>
                                            <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)" :disabled="concepto == ''"></i>
                                            <i class="far fa-copy button" v-on:click="copiar_destino(partida)" title="Copiar" ></i>
                                            <i class="fas fa-paste button" v-on:click="pegar_destino(partida)" title="Pegar"></i>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="observaciones" class="col-form-label">Observaciones: </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row error-content">
                                <textarea
                                    name="observaciones"
                                    id="observaciones"
                                    class="form-control"
                                    v-model="factura.observaciones"
                                    v-validate="{required: true}"
                                    data-vv-as="Observaciones"
                                    :class="{'is-invalid': errors.has('observaciones')}"
                                ></textarea>
                            <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 offset-5">
                        <div class="form-group row error-content">
                            <label for="tipo_gasto_select" class="col-sm-3 col-form-label"><b>Tipo de Gasto: </b></label>
                            <div class="col-sm-9">
                                <model-list-select
                                    name="tipo_gasto_select"
                                    data-vv-as="Tipo de Gasto"
                                    option-value="id"
                                    class="form-control"
                                    option-text="descripcion"
                                    v-model="factura.id_costo"
                                    :list="tipo_gasto"
                                    :placeholder="!cargando?'Seleccionar':'Cargando...'"
                                    :isError="errors.has(`tipo_gasto_select`)"
                                    id="tipo_gasto_select">
                                </model-list-select>
                                <div class="invalid-feedback" v-show="errors.has('tipo_gasto_select')">{{ errors.first('tipo_gasto_select') }}</div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><b>Subtotal: </b></label>
                            subtotal
                        </div>
                    </div>
                    <div class="col-md-4 offset-5">
                        <div class="form-group">
                            <label><b>Moneda: </b></label>
                            {{factura.moneda}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><b>IVA Subtotal: </b></label>
                            input
                        </div>
                    </div>
                    <div class="col-md-4 offset-5">
                        <div class="form-group">
                            <label><b>Monto Original: </b></label>
                            {{factura.monto_format}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><b>Ret. IVA (4%): </b></label>
                            input
                        </div>
                    </div>
                    <div class="col-md-4 offset-5">
                        <div class="form-group">
                            <label><b>Diferencia: </b></label>
                            {{getDiferencia()}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><b>Ret. IVA (6%): </b></label>
                            input
                        </div>
                    </div>
                    <div class="col-md-3 offset-9">
                        <div class="form-group">
                            <label><b>Ret. IVA (2/3): </b></label>
                            input
                        </div>
                    </div>
                    <div class="col-md-3 offset-9">
                        <div class="form-group">
                            <label><b>IVA A Pagar: </b></label>
                            iva a pagar
                        </div>
                    </div>
                    <div class="col-md-3 offset-9">
                        <div class="form-group">
                            <label><b>IEPS: </b></label>
                            input
                        </div>
                    </div>
                    <div class="col-md-3 offset-9">
                        <div class="form-group">
                            <label><b>Imp. Hosp.: </b></label>
                            input
                        </div>
                    </div>
                    <div class="col-md-3 offset-9">
                        <div class="form-group">
                            <label><b>Ret. ISR (10%): </b></label>
                            input
                        </div>
                    </div>
                    <div class="col-md-3 offset-9">
                        <div class="form-group">
                            <label><b>Total: </b></label>
                            total
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" ref="modal_destino" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal-destino"> <i class="fa fa-sign-in"></i> Seleccionar Destino</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row error-content">
                                            <label for="id_concepto" class="col-sm-2 col-form-label">Conceptos:</label>
                                            <div class="col-sm-10">
                                                <concepto-select
                                                    name="id_concepto"
                                                    data-vv-as="Concepto"
                                                    id="id_concepto"
                                                    v-model="id_concepto_temporal"
                                                    :error="errors.has('id_concepto')"
                                                    ref="conceptoSelect"
                                                    :disableBranchNodes="nodo"
                                                ></concepto-select>
                                                <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            </div>
                            <div class="modal-footer">
                                <button  type="button"  class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fa fa-close"  ></i> Cerrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import ConceptoSelect from "../../cadeco/concepto/Select";
import {ModelListSelect} from 'vue-search-select';
export default {
    name: "facturas-varios-revision",
    components: {ConceptoSelect,ModelListSelect},
    props:['id'],
    data(){
        return{
            cargando:false,
            nodo:true,
            index:'',
            concepto:'',
            factura:'',
            observaciones:'',
            tipo_gasto:{},
            id_concepto_temporal:'',
            conceptos:[
                'Comisiones e Intereses Bancarios',
                'Deducibles por Siniestros',
                'Depreciaciones y Amortizaciones',
                'Guarantee Fee y Cuota Corporativa',
                'Intereses y Préstamos',
                'Pago de Impuestos, Derechos, Multas',
                'Seguros y Fianzas',
            ],
            servicios:[
                'Agua',
                'Luz',
                'Internet',
                'Teléfono',
                'Honorarios No Recurrentes (Despachos Externos)',
            ],
            partidas: [
                {
                    i : 0,
                    concepto : "",
                    cantidad : "",
                    precio : "",
                    monto : "",
                    destino : "",
                }
            ],
        }
    },
    mounted() {
        this.find();
        this.getTipoGasto();
    },
    methods : {
        addPartidas(){
            this.partidas.splice(this.partidas.length + 1, 0, {
                concepto : "",
                    cantidad : "",
                    precio : "",
                    monto : "",
                    destino : "",
            });
        },
        find() {
            this.cargando = true;
            return this.$store.dispatch('finanzas/factura/find', {
                id: this.id,
                params: {include: ['complemento', 'cambio.moneda', 'contra_recibo']}
            }).then(data => {
                this.factura = data
            }).finally(() => {
                this.cargando = false;
            })
        },
        getConcepto() {
            return this.$store.dispatch('cadeco/concepto/find', {
                id: this.id_concepto_temporal,
                params: {
                }
            })
            .then(data => {
                if(this.index == -1){
                    this.concepto = data;
                    this.nodo = true;
                }else{
                    this.partidas[this.index].destino = data;
                }
                $(this.$refs.modal_destino).modal('hide');
            })
        },
        getDiferencia(){
            return '$ ' + parseFloat(this.factura.monto).toFixed(2);
        },
        getMonto(partida){
            return '$ ' + parseFloat(partida.cantidad * partida.precio).toFixed(2); 
        },
        getTipoGasto(){
            this.tipo_gasto = [];
            return this.$store.dispatch('cadeco/costo/index', {
                id: this.id,
                params:{
                    scope: ['datosContablesConfiguracion'], sort: 'descripcion', order: 'ASC'
                }
            }).then(data => {
                this.tipo_gasto = data.data;
            })
        },
        modalDestino(i) {
            if(i == -1) this.nodo = false;
            this.index = i;
            this.$validator.reset();
            $(this.$refs.modal_destino).modal('show');
        }
    },
     watch: {
            id_concepto_temporal(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.getConcepto();
                }
            },
        }
}
</script>

<style>

</style>