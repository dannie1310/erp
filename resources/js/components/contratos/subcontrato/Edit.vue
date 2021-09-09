<template>
    <span>
        <div class="card" v-if="cargando">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <encabezado-subcontrato v-bind:subcontrato="subcontratos"></encabezado-subcontrato>
                    </div>
                </div>
                <div class="row">
                    <div class=" col-md-2" align="left">
                        <label class="col-md-12 col-form-label" >Contratista:</label>
                    </div>
                    <div class=" col-md-10" align="left">
                    {{subcontratos.empresa}}
                    </div>
                </div>
                <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-md-12 col-form-label" for="fecha" >Fecha:</label>
                        </div>
                        <div class=" col-md-2" align="left">
                            <datepicker v-model = "subcontratos.fecha"
                                        id="fecha"
                                        name = "fecha"
                                        :format = "formatoFecha"
                                        :language = "es"
                                        :bootstrap-styling = "true"
                                        class = "col-sm-10 form-control"
                                        v-validate="{required: true}"
                                        :class="{'is-invalid': errors.has('fecha')}"
                            ></datepicker>
                        </div>
                        <div class=" col-md-2" align="left">
                            <label class="col-md-12 col-form-label" >Referencia:</label>
                        </div>
                        <div class=" col-md-6" align="left">
                            <input
                                type="text"
                                name="referencia"
                                data-vv-as="Referencia"
                                v-validate="{required: true, max: 64}"
                                class="form-control"
                                id="referencia"
                                placeholder="Referencia"
                                v-model="subcontratos.referencia"
                                :class="{'is-invalid': errors.has('referencia')}">
                                <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                        </div>
                    </div>
                <br />
                <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-md-12 col-form-label" >Descripción:</label>
                        </div>
                        <div class=" col-md-10" align="left">
                            <input
                                type="text"
                                name="descripcion"
                                data-vv-as="Descripcion"
                                v-validate="{required: true}"
                                class="form-control"
                                id="descripcion"
                                placeholder="Descripcion"
                                v-model="descripcion"
                                :class="{'is-invalid': errors.has('descripcion')}">
                                <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                        </div>
                    </div>
                <hr>

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
                                    <tr v-for="(partida, i) in subcontratos.partidas.data">
                                        <td>{{i+1}}</td>
                                        <td class="td_numero_folio">{{partida.contratos.clave}}</td>
                                        <td>{{partida.contratos.descripcion}}</td>
                                        <td align="center">{{partida.contratos.unidad}}</td>
                                        <td class="td_money">{{partida.cantidad_format}}</td>
                                        <td class="td_money">{{partida.precio_unitario_format}}</td>
                                        <td class="td_money">{{partida.importe_total}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="row">
                        <div class=" col-md-12" align="right">
                            <label class="col-md-3 col-form-label" style="text-align: right">Subtotal Antes de Descuento:</label>
                            <label class="col-md-2 col-form-label" style="text-align: right">{{subcontratos.subtotal_antes_descuento}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: right">% Descuento:</label>
                            <label class="col-md-2 col-form-label" style="text-align: right">{{subcontratos.descuento}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: right">Subtotal:</label>
                            <label class="col-md-2 col-form-label" style="text-align: right">{{subcontratos.subtotal_format}}</label>
                        </div>
                        <div class=" col-md-10" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: right">IVA:</label>
                        </div>
                        <div class=" col-md-2 p-1" align="right">
                            <input
                                type="text"
                                name="iva"
                                v-model="subcontratos.impuesto"
                                v-validate="{required: true, min_value:0, regex: /^[0-9]\d*(\.\d+)?$/}"
                                class="col-sm-8 form-control"
                                id="iva"
                                style="text-align: right"
                                :class="{'is-invalid': errors.has('iva')}">
                        </div>
                        <div class=" col-md-10" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: right">Retención IVA:</label>
                        </div>
                        <div class=" col-md-2 p-1" align="right">
                            <input
                                type="text"
                                name="retencion_iva"
                                v-model="subcontratos.retencion_iva"
                                v-validate="{required: true, min_value:0, max_value:subcontratos.impuesto, regex: /^[0-9]\d*(\.\d+)?$/}"
                                class="col-sm-8 form-control"
                                id="retencion_iva"
                                style="text-align: right"
                                :class="{'is-invalid': errors.has('retencion_iva')}">
                        </div>
                         <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: right">Total:</label>
                            <label class="col-md-2 col-form-label" style="text-align: right">{{total}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: right">Moneda:</label>
                            <label class="col-md-2 col-form-label" style="text-align: right">{{subcontratos.moneda.nombre}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: right">Anticipo({{subcontratos.anticipo_format}}):</label>
                            <label class="col-md-2 col-form-label" style="text-align: right">{{subcontratos.anticipo_monto_format}}</label>
                        </div>
                        <div class=" col-md-10" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: right">% Fondo de Garantía:</label>
                        </div>
                        <div class=" col-md-2 p-1" align="right">
                            <input
                                type="text"
                                name="retencion_fg"
                                v-model="subcontratos.retencion_fg"
                                v-validate="{required: true, min_value:0, max_value:100, regex: /^[0-9]\d*(\.\d+)?$/}"
                                class="col-sm-8 form-control"
                                id="retencion_fg"
                                style="text-align: right"
                                :class="{'is-invalid': errors.has('retencion_fg')}">
                        </div>

                    </div>
                    <hr>
                    <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-md-12 col-form-label" >Plazo de Ejecución:</label>
                        </div>
                        <div class=" col-md-1" style="padding-top: calc(0.375rem + 1px)" >
                            Del:
                        </div>
                        <div class="col-md-2">
                            <div class="form-group  ">
                                <datepicker v-model = "plazo_ejecucion.fecha_ini_ejec"
                                            id="fecha_ini"
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
                        <div class=" col-md-1" style="padding-top: calc(0.375rem + 1px)" >
                            Al:
                        </div>
                        <div class="col-md-2">
                            <div class="form-group  ">
                                <datepicker v-model = "plazo_ejecucion.fecha_fin_ejec"
                                            id="fecha_fin"
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
                     <br>
                    <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-md-12 col-form-label" >Tipo de Gasto:</label>
                        </div>
                        <div class=" col-md-6" align="left">
                           <model-list-select
                               :disabled="cargando"
                               name="id_costo"
                               v-model="subcontratos.id_costo"
                               option-value="id"
                               option-text="descripcion"
                               :list="tipos_gasto"
                               :placeholder="!cargando?'Seleccionar o buscar por descripcion':'Cargando...'"
                               :isError="errors.has(`id_costo`)">
                            </model-list-select>
                        </div>


                    </div><br>
                     <br>
                     <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-sm-12 col-form-label" >Tipo de Contrato:</label>
                        </div>
                        <div class=" col-md-6" align="left" >
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
                     <br>
                    <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-md-12 col-form-label" >Personalidad Contratista:</label>
                        </div>
                        <div class=" col-md-10" align="left">
                            {{subcontratos.personalidad_contratista}}
                        </div>
                    </div>

            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary float-right" style="margin-left:5px" @click="validate"><i class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-secondary float-right" @click=cerrar()><i class="fa fa-angle-left"></i>Regresar</button>

                    </div>
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
    import DatosSubcontrato from "./partials/DatosSubcontrato";
    import EncabezadoSubcontrato from "./partials/EncabezadoSubcontrato";
    export default {
        name: "subcontrato-edit",
        components: {EncabezadoSubcontrato, DatosSubcontrato, datepicker, CostoSelect, ModelListSelect},
        props: ['id'],
        data() {
            return {
                es: es,
                cargando:false,
                buscando:false,
                subcontratos: '',
                tipo_contrato:[],
                tipos_gasto:[],
                id_tipo_contrato:'',
                id_costo:'',
                descripcion:'',
                plazo_ejecucion:{
                    fecha_ini_ejec:'',
                    fecha_fin_ejec:'',
                }
            }
        },
        mounted(){

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
                        /*data.subcontratos = {
                            id:null,
                            descripcion:'',
                        };*/
                    }else{
                        this.descripcion = data.subcontratos.observacion;
                        this.plazo_ejecucion.fecha_ini_ejec = data.subcontratos.fecha_ini_ejec;
                        this.plazo_ejecucion.fecha_fin_ejec = data.subcontratos.fecha_fin_ejec;
                    }
                }).finally(()=>{
                    this.getTipoContrato();
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
                }).finally(()=>{
                    this.getTiposGasto();
                })
            },
            getTiposGasto(){
                this.tipos_gasto = [];
                return this.$store.dispatch('cadeco/costo/index', {
                    id: this.id,
                    params:{
                        sort: 'descripcion', order: 'ASC'
                    }
                }).then(data => {
                    this.tipos_gasto = data.data;
                }).finally(()=>{
                    this.cargando = false;
                })
            },
            cerrar(){
                swal({
                    title: "Salir de la Edición de Subcontrato",
                    text: "¿Está seguro de querer salir de la edición del subcontrato?",
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
                    observacion: this.descripcion,
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
