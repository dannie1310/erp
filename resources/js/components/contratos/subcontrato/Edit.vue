<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3" v-if="cargando">
                    <div class="row">
                        <div class="col-12">
                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                        </div>
                    </div>
                </div>
                <div class="invoice p-3 mb-3" v-if="subcontratos">
                    <div class="row">
                        <div class="col-6">
                            <h5>Subcontrato: <b>{{subcontratos.numero_folio_format}}</b></h5>
                        </div>
                        <div class="col-6">
                            <h5>Contrato: <b>{{subcontratos.contrato_folio_format}}</b></h5>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-7 mt-2 ml-3">
                            <div class="form-group  ">
                                <label for="razon_social" class="mr-1" ><b>Referencia: </b> </label>
                                <input
                                    style="text-transform:uppercase;"
                                    type="text"
                                    name="referencia"
                                    data-vv-as="Referencia"
                                    v-validate="{required: true}"
                                    class="form-control"
                                    id="referencia"
                                    placeholder="Referencia"
                                    v-model="subcontratos.referencia"
                                    :class="{'is-invalid': errors.has('referencia')}">
                                <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>

                                
                            </div>
                            
                        </div>
                        <div class="col-md-4 mt-2 ml-3">
                            <div class="form-group  ">
                                <label for="razon_social" class="mr-1" ><b>Fecha: </b> </label>
                                <datepicker v-model = "subcontratos.fecha"
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
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6><b>Detalle de las partidas</b></h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Clave</th>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                        <th>Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(doc, i) in subcontratos.partidas.data">
                                        <td>{{i+1}}</td>
                                        <td class="td_numero_folio"></td>
                                        <td>{{doc.contratos.descripcion}}</td>
                                        <td align="center">{{doc.contratos.unidad}}</td>
                                        <td class="td_money">{{doc.cantidad_format}}</td>
                                        <td class="td_money">{{doc.precio_unitario_format}}</td>
                                        <td class="td_money">{{doc.importe_total}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="row">
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Subtotal Antes Descuento:</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.subtotal_antes_descuento}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Descuento(%):</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.descuento}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Subtotal:</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.subtotal_format}}</label>
                        </div>
                        <div class=" col-md-10" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">IVA:</label>
                        </div>
                        <div class=" col-md-2 p-1" align="right">
                            <input
                                type="number"
                                step="any"
                                name="iva"
                                v-model="subcontratos.impuesto"
                                v-validate="{required: true, min_value:0}"
                                class="col-sm-8 form-control"
                                id="iva"
                                :class="{'is-invalid': errors.has('iva')}">
                        </div>
                        <div class=" col-md-10" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Retención IVA:</label>
                        </div>
                        <div class=" col-md-2 p-1" align="right">
                            <input
                                type="number"
                                step="any"
                                name="retencion_iva"
                                v-model="subcontratos.retencion_iva"
                                v-validate="{required: true, min_value:0, max_value:subcontratos.impuesto}"
                                class="col-sm-8 form-control"
                                id="retencion_iva"
                                :class="{'is-invalid': errors.has('retencion_iva')}">
                        </div>
                         <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Total:</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{total}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Moneda:</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.moneda.nombre}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Anticipo({{subcontratos.anticipo_format}}):</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.anticipo_monto_format}}</label>
                        </div>
                        <div class=" col-md-10" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">% Fondo de Garantía:</label>
                        </div>
                        <div class=" col-md-2 p-1" align="right">
                            <input
                                type="number"
                                step="any"
                                max="100"
                                name="retencion_fg"
                                v-model="subcontratos.retencion_fg"
                                v-validate="{required: true}"
                                class="col-sm-8 form-control"
                                id="retencion_fg"
                                :class="{'is-invalid': errors.has('retencion_fg')}">
                        </div>
                        
                    </div>
                    <hr>
                    <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-sm-12 mt-4 col-form-label" style="text-align: right">Plazo de Ejecución:</label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <div class="form-group  ">
                                <label for="fecha_ini" class="mr-1" ><b>De: </b> </label>
                                <datepicker v-model = "plazo_ejecucion.fecha_ini_ejec"
                                            name = "fecha_ini"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "col-sm-10 form-control"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('fecha_ini')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha_ini') }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 mt-2">
                            <div class="form-group  ">
                                <label for="fecha_fin" class="mr-1" ><b>Al: </b> </label>
                                <datepicker v-model = "plazo_ejecucion.fecha_fin_ejec"
                                            name = "fecha_fin"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "col-sm-10 form-control"
                                            
                                            v-validate="{required: true}"
                                            :disabled-dates="fechasDeshabilitadas"
                                            :class="{'is-invalid': errors.has('fecha_fin')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha_fin') }}</div>
                            </div>
                        </div> 
                    </div>
                    
                    <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-sm-12 col-form-label" style="text-align: right">Tipo de Gasto:</label>
                        </div>
                        <div class=" col-md-3" align="left">
                            <label class="col-form-label" style="text-align: left">{{costo}}</label>
                        </div>
                        <div class=" col-md-1" >
                            <small class="badge badge-secondary">
                                <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="cambiarGasto()" ></i>
                            </small>
                            <!-- <button type="button" class="btn btn-app btn-info float-right" @click="cambiarGasto()"><i class="fa fa-sign-in button"></i></button> -->
                        </div>
                        <div class=" col-md-2" align="left">
                            <label class="col-sm-12 col-form-label" style="text-align: right">Tipo de Contrato:</label>
                        </div>
                        <div class=" col-md-4" align="left" >
                            <model-list-select 
                                    :disabled="cargando"
                                    name="id_tipo_contrato"
                                    v-model="subcontratos.id_tipo_contrato"
                                    option-value="id"
                                    option-text="descripcion"
                                    :list="tipo_contrato"
                                     v-validate="{required: true}"
                                    :placeholder="!cargando?'Seleccionar o buscar por descripcion':'Cargando...'"
                                    :isError="errors.has(`id_tipo_contrato`)">
                            </model-list-select>
                            <div class="invalid-feedback" v-show="errors.has('id_tipo_contrato')">{{ errors.first('id_tipo_contrato') }}</div>
                        </div>
                    </div><br>
                    <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-sm-12 col-form-label" style="text-align: right">Descripción:</label>
                        </div>
                        <div class=" col-md-10" align="left">
                            <input
                                    type="text"
                                    name="observaciones"
                                    data-vv-as="Observaciones"
                                    v-validate="{required: true}"
                                    class="form-control"
                                    id="observaciones"
                                    placeholder="Observaciones"
                                    v-model="subcontratos.subcontratos.descripcion"
                                    :class="{'is-invalid': errors.has('observaciones')}">
                                <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                        </div>
                    </div> <br>
                    <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-sm-12 col-form-label" style="text-align: right">Personalidad Contratista:</label>
                        </div>
                        <div class=" col-md-10" align="left">
                            <label class="col-form-label" style="text-align: left">{{subcontratos.personalidad_contratista}}</label>
                        </div>
                    </div>
                    <br><br>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary float-right" style="margin-left:5px" @click="validate"> Guardar</button>
                            <button type="button" class="btn btn-secondary float-right" @click=cerrar()>Cerrar</button>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">CAMBIAR TIPO GASTO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div role="form">
                                <costo-select
                                    name="id_costo"
                                    data-vv-as="Costo"
                                    v-validate="{}"
                                    id="id_costo"
                                    v-model="id_costo"
                                    :error="errors.has('id_costo')"
                                    ref="costoSelect"
                                ></costo-select>
                                <div class="error-label" v-show="errors.has('id_costo')">{{ errors.first('id_costo') }}</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" @click="cambiar()" class="btn btn-primary"><i class="fa fa-spin fa-spinner" v-if="buscando"></i>Cambiar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </span>
</template>

<script>
    import datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import CostoSelect from "../../cadeco/costo/Select";
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "subcontrato-edit",
        components: {datepicker, CostoSelect, ModelListSelect},
        props: ['id'],
        data() {
            return {
                es: es,
                cargando:false,
                buscando:false,
                subcontratos: '',
                tipo_contrato:[],
                id_tipo_contrato:'',
                id_costo:'',
                plazo_ejecucion:{
                    fecha_ini_ejec:'',
                    fecha_fin_ejec:'',
                }
            }
        },
        mounted(){
            this.getTipoContrato();
            this.find();
            
        },
        methods: {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            find() {
                this.cargando = true;
                return this.$store.dispatch('contratos/subcontrato/find', {
                    id: this.id,
                    params: {include: ['partidas', 'moneda', 'partidas.contratos', 'subcontratos', 'costo']}
                }).then(data => {
                    this.subcontratos = data;
                    if(!data.subcontratos){
                        data.subcontratos = {
                            id:null,
                            descripcion:'',
                        };
                    }else{
                        this.plazo_ejecucion.fecha_ini_ejec = data.subcontratos.fecha_ini_ejec;
                        this.plazo_ejecucion.fecha_fin_ejec = data.subcontratos.fecha_fin_ejec;
                    }
                }).finally(()=>{
                    this.cargando = false;
                });
            },
            getTipoContrato(){
                this.tipo_contrato = [];
                return this.$store.dispatch('contratos/tipo-contrato/index', {
                        id: this.id,
                        params:{
                           sort: 'id_tipo_contrato', order: 'ASC'
                        }
                    }).then(data => {
                        this.tipo_contrato = data;
                    })
            },
            cambiarGasto(){
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            cambiar(){
                this.buscando = true;
                return this.$store.dispatch('cadeco/costo/find', {
                    id: this.id_costo,
                }).then(data => {
                    this.subcontratos.costo = data;
                }).finally(()=>{
                    this.subcontratos.id_costo = this.id_costo;
                    this.id_costo = '';
                    this.buscando = false;
                    $(this.$refs.modal).modal('hide');
                });
            },
            cerrar(){
                swal({
                    title: "Cerrar Editar Subcontrato",
                    text: "¿Está seguro/a de que quiere salir de la edición de subcontrato?",
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
                        this.$router.push({name: 'subcontrato'});
                    }
                });
            },
            updateSubcontrato(){
                this.cargando = true;
                let data = {
                    referencia: this.subcontratos.referencia,
                    fecha:  this.subcontratos.fecha,
                    impuesto:  this.subcontratos.impuesto,
                    retencion_iva:  this.subcontratos.retencion_iva,
                    monto:  this.monto,
                    retencion_fg:  this.subcontratos.retencion_fg,
                    fecha_ini_ejec:  this.plazo_ejecucion.fecha_ini_ejec,
                    fecha_fin_ejec:  this.plazo_ejecucion.fecha_fin_ejec,
                    id_costo: this.subcontratos.id_costo,
                    id_tipo_contrato:  this.subcontratos.id_tipo_contrato,
                    observacion: this.subcontratos.subcontratos.descripcion,
                };
                return this.$store.dispatch('contratos/subcontrato/updateContrato',
                    {
                        id:this.id,
                        data: data,
                    })
                    .then(data => {
                        this.$router.push({name: 'subcontrato'});
                    }).finally(() => {
                        this.cargando = false;
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.updateSubcontrato();
                    }
                });
            },
        },
        computed: {
            fechasDeshabilitadas() {
                if(this.plazo_ejecucion.fecha_ini_ejec != ''){
                    
                    return{
                        to: new Date( this.plazo_ejecucion.fecha_ini_ejec)
                    }; 
                }
                return {};
            },
            total(){
                if(this.subcontratos){
                    return '$ '+parseFloat(this.subcontratos.subtotal + (this.subcontratos.impuesto - this.subcontratos.retencion_iva)).formatMoney(2,'.',','); 
                }
                return 0;
            },
            monto(){
                if(this.subcontratos){
                    return this.subcontratos.subtotal + (this.subcontratos.impuesto - this.subcontratos.retencion_iva); 
                }
                return 0;
            },
            costo(){
                if(this.subcontratos.costo){
                    return this.subcontratos.costo.descripcion;
                }
                return '';
            },
        }
    }
</script>

<style>

</style>