<template>
    <span>
        <nav>
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
                            <datepicker v-model = "fecha"
                                        name = "fecha"
                                        :format = "formatoFecha"
                                        :language = "es"
                                        :bootstrap-styling = "true"
                                        class = "form-control"
                                        v-validate="{required: true}"
                                        :class="{'is-invalid': errors.has('fecha')}"
                            ></datepicker>
                            <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
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
                            Gastos Varios
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
                        <div class="form-group row">
                            <label class="col-md-1"><b>Concepto: </b></label>
                            <concepto-select
                                class="col-md-11"
                                name="id_concepto"
                                data-vv-as="Concepto"
                                v-validate="{required: true}"
                                id="id_concepto"
                                v-model="id_concepto"
                                :error="errors.has('id_concepto')"
                                ref="conceptoSelect"
                                :disableBranchNodes="false"
                                onselect="findConcepto"
                            ></concepto-select>
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
                                                    v-on:keyup="actualizar_resumen()"
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
                                                    v-on:keyup="actualizar_resumen()"
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
                                    v-validate="{}"
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
                        <div class="form-group row">
                            <label class="col-sm-5"><b>Subtotal: </b></label>
                            {{getSubtotal()}}
                        </div>
                    </div>
                    <div class="col-md-4 offset-5">
                        <div class="form-group row">
                            <label class="col-sm-3"><b>Moneda: </b></label>
                            {{factura.moneda}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label"><b>IVA Subtotal: </b></label>
                            <div class="col-sm-7">
                                <input type="number"
                                    min="0"
                                    step=".01"
                                    class="form-control"
                                    name="iva_subtotal"
                                    data-vv-as="IVA Subtotal"
                                    v-validate="{required: true}"
                                    v-on:keyup="actualizar_resumen()"
                                    :class="{'is-invalid': errors.has('iva_subtotal')}"
                                    v-model="resumen.iva_subtotal"/>
                                <div class="invalid-feedback" v-show="errors.has('iva_subtotal')">{{ errors.first('iva_subtotal') }}</div>
                            </div>                            
                        </div>
                    </div>
                    <div class="col-md-4 offset-5">
                        <div class="form-group row">
                            <label class="col-sm-3"><b>Monto Original: </b></label>
                            {{factura.monto_format}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label"><b>Ret. IVA (4%): </b></label>
                            <div class="col-sm-7">
                                <input type="number"
                                    min="0"
                                    step=".01"
                                    class="form-control"
                                    name="ret_iva_4"
                                    data-vv-as="Ret. IVA (4%)"
                                    v-validate="{}"
                                    :class="{'is-invalid': errors.has('ret_iva_4')}"
                                    v-on:keyup="actualizar_resumen()"
                                    v-model="resumen.ret_iva_4"/>
                            <div class="invalid-feedback" v-show="errors.has('ret_iva_4')">{{ errors.first('ret_iva_4') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 offset-5">
                        <div class="form-group row">
                            <label class="col-sm-3"><b>Diferencia: </b></label>
                            {{getDiferencia()}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label"><b>Ret. IVA (6%): </b></label>
                            <div class="col-sm-7">
                                <input type="number"
                                    min="0"
                                    step=".01"
                                    class="form-control"
                                    name="ret_iva_6"
                                    data-vv-as="Ret. IVA (6%)"
                                    v-validate="{}"
                                    :class="{'is-invalid': errors.has('ret_iva_6')}"
                                    v-on:keyup="actualizar_resumen()"
                                    v-model="resumen.ret_iva_6"/>
                                <div class="invalid-feedback" v-show="errors.has('ret_iva_6')">{{ errors.first('ret_iva_6') }}</div>
                            </div>                            
                        </div>
                    </div>
                    <div class="col-md-3 offset-9">
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label"><b>Ret. IVA (2/3): </b></label>
                            <div class="col-sm-7">
                                <input type="number"
                                    min="0"
                                    step=".01"
                                    class="form-control"
                                    name="ret_iva_2_3"
                                    data-vv-as="Ret. IVA (2/3)"
                                    v-validate="{}"
                                    :class="{'is-invalid': errors.has('ret_iva_2_3')}"
                                    v-on:keyup="actualizar_resumen()"
                                    v-model="resumen.ret_iva_2_3"/>
                                <div class="invalid-feedback" v-show="errors.has('ret_iva_2_3')">{{ errors.first('ret_iva_2_3') }}</div>
                            </div>                           
                        </div>
                    </div>
                    <div class="col-md-3 offset-9">
                        <div class="form-group row">
                            <label class="col-sm-5"><b>IVA A Pagar: </b></label>
                            {{getIvaPagar()}}
                        </div>
                    </div>
                    <div class="col-md-3 offset-9">
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label"><b>IEPS: </b></label>
                            <div class="col-sm-7">
                                <input type="number"
                                    min="0"
                                    step=".01"
                                    class="form-control"
                                    name="ieps"
                                    data-vv-as="IEPS"
                                    v-validate="{}"
                                    :class="{'is-invalid': errors.has('ieps')}"
                                    v-on:keyup="actualizar_resumen()"
                                    v-model="resumen.ieps"/>
                                <div class="invalid-feedback" v-show="errors.has('ieps')">{{ errors.first('ieps') }}</div>
                            </div>                           
                        </div>
                    </div>
                    <div class="col-md-3 offset-9">
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label"><b>Imp. Hosp.: </b></label>
                            <div class="col-sm-7">
                                <input type="number"
                                    min="0"
                                    step=".01"
                                    class="form-control"
                                    name="imp_hosp"
                                    data-vv-as="Imp. Hosp."
                                    v-validate="{}"
                                    :class="{'is-invalid': errors.has('imp_hosp')}"
                                    v-on:keyup="actualizar_resumen()"
                                    v-model="resumen.imp_hosp"/>
                                <div class="invalid-feedback" v-show="errors.has('imp_hosp')">{{ errors.first('imp_hosp') }}</div>
                            </div>                          
                        </div>
                    </div>
                    <div class="col-md-3 offset-9">
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label"><b>Ret. ISR (10%): </b></label>
                            <div class="col-sm-7">
                                <input type="number"
                                    min="0"
                                    step=".01"
                                    class="form-control"
                                    name="ret_isr"
                                    data-vv-as="Ret. ISR (10%)"
                                    v-validate="{}"
                                    :class="{'is-invalid': errors.has('ret_isr')}"
                                    v-on:keyup="actualizar_resumen()"
                                    v-model="resumen.ret_isr"/>
                                <div class="invalid-feedback" v-show="errors.has('ret_isr')">{{ errors.first('ret_isr') }}</div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-3 offset-9">
                        <div class="form-group row">
                            <label class="col-sm-5"><b>Total: </b></label>
                            {{getTotal()}}
                        </div>
                    </div>
                </div>
                 <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"  v-on:click="salir" >Cerrar</button>
                        <button type="submit" class="btn btn-primary" @click="validate">Guardar</button>
                    </div>
            </div>
        </div>
        </nav>
        <nav>
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
                                    <div class="col-12" v-if="id_concepto && concepto.id>0 && concepto.id !==undefined">
                                        <div class="form-group row error-content">
                                            <label for="id_concepto" class="col-sm-2 col-form-label">Conceptos:</label>
                                            <div class="col-sm-10">
                                                <ConceptoSelectHijo
                                                        name="id_conceptos"
                                                        data-vv-as="Concepto"
                                                        id="id_conceptos"
                                                        v-model="id_concepto_temporal"
                                                        ref="conceptoSelect"
                                                        :disableBranchNodes="true"
                                                        v-bind:nivel_id="concepto.id"
                                                ></ConceptoSelectHijo>
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
        </nav>
    </span>
</template>

<script>
import ConceptoSelect from "../../cadeco/concepto/Select";
import {ModelListSelect} from 'vue-search-select';
import datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';
import ConceptoSelectHijo from "../../cadeco/concepto/SelectHijo";
export default {
    name: "facturas-varios-revision",
    components: {ConceptoSelect,ModelListSelect,datepicker,ConceptoSelectHijo},
    props:['id'],
    data(){
        return{
            es: es,
            cargando:false,
            nodo:false,
            index:-1,
            concepto:'',
            factura:'',
            fecha:'',
            observaciones:'',
            tipo_gasto:{},
            id_concepto:'',
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
            resumen:{
                subtotal:0,
                iva_subtotal:0,
                ret_iva_4:0,
                ret_iva_6:0,
                ret_iva_2_3:0,
                iva_pagar:0,
                ieps:0,
                imp_hosp:0,
                ret_isr:0,
                total:0,
            },
        }
    },
    mounted() {
        this.find();
        this.getTipoGasto();
    },
    methods : {
        actualizar_resumen(){
            let self = this;
            if(this.partidas){
                this.resumen.subtotal = 0;
                this.partidas.forEach(partida => {
                    this.resumen.subtotal = parseFloat(this.resumen.subtotal + (partida.cantidad * partida.precio));
                    partida.monto = parseFloat(partida.cantidad * partida.precio);
                });
                self.resumen.iva_subtotal = parseFloat(self.resumen.subtotal * 0.16).toFixed(2);
                self.resumen.iva_pagar =  parseFloat(self.resumen.iva_subtotal - self.resumen.ret_iva_4 - self.resumen.ret_iva_6 - self.resumen.ret_iva_2_3);
                self.resumen.total = parseFloat(self.resumen.subtotal + self.resumen.iva_pagar) + parseFloat(self.resumen.ieps) + parseFloat(self.resumen.imp_hosp) - parseFloat(self.resumen.ret_isr);
            }
        },
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
                this.fecha = data.fecha;
                this.factura = data
            }).finally(() => {
                this.cargando = false;
            })
        },
        findConcepto(value) {
            this.concepto = '';
            if(value !== undefined && value !== null){
                return this.$store.dispatch('cadeco/concepto/find', {
                    id: value,
                    params: {}
                }).then(data => {
                    this.concepto = data;
                });
            } else {
                this.partidas.forEach(function(partida) {
                    partida.destino = '';
                });
            }
        },
        formatoFecha(date){
            return moment(date).format('DD-MM-YYYY');
        },
        getConcepto(id) {
            return this.$store.dispatch('cadeco/concepto/find', {
                id: id,
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
                this.index = -1;
                // $(this.$refs.modal_concepto).modal('hide');
                $(this.$refs.modal_destino).modal('hide');
            })
        },
        getDiferencia(){
            return '$ ' + parseFloat(this.resumen.total - this.factura.monto).formatMoney(2);
        },
        getMonto(partida){
            return '$ ' + parseFloat(partida.cantidad * partida.precio).formatMoney(2); 
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
        getSubtotal(){
            return '$ ' + parseFloat(this.resumen.subtotal).formatMoney();
        },
        getIvaPagar(){
            return '$ ' + parseFloat(this.resumen.iva_pagar).formatMoney();
        },
        getTotal(){
            return '$ ' + parseFloat(this.resumen.total).formatMoney();
        },
        modalDestino(i) {
            this.index = i;
            this.$validator.reset();
            if(i >= 0) {
                this.nodo = true;
                $(this.$refs.modal_destino).modal('show');
            }
            
        },
        salir(){
            this.$router.push({name: 'factura'});
        },
        store(){
            return this.$store.dispatch('finanzas/factura/storeRevisionVarios', {
                factura:this.factura,
                id_concepto:this.id_concepto,
                resumen:this.resumen,
                fecha:this.fecha,
                partidas:this.partidas,
                diferencia:parseFloat(this.resumen.total_documentos - this.factura.monto),
            })
                .then((data) => {
                    this.$router.push({name: 'factura'});
                });
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result){
                    let destino = false;
                    this.partidas.forEach(partida => {
                        destino = partida.destino==="";
                    });
                    if(destino){
                        swal('¡Error!', 'Ingrese un destino en todas las partidas seleccionadas.', 'error');
                    }
                    else if(Math.abs(parseFloat(this.resumen.total - this.factura.monto)) > 0.99){
                        swal('¡Error!', 'Para proceder con la revisión, la diferencia debe ser menor a 0.99', 'error')
                    }else{
                        this.store()
                    }
                    
                }
            });
        },
    },
     watch: {
            id_concepto_temporal(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.getConcepto(value);
                }
            },
            id_concepto(value){
                if(value != '' && value !== null && value !== undefined){
                    this.findConcepto(value);
                }
            },
        }
}
</script>

<style>

</style>