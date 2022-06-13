<template>
     <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group error-content">
                                                <label for="fecha" class="col-form-label">Fecha:</label>

                                                    <datepicker v-model = "fecha"
                                                                    name = "fecha"
                                                                    :format = "formatoFecha"
                                                                    :language = "es"
                                                                    :bootstrap-styling = "true"
                                                                    class = "form-control"
                                                                    v-validate="{required: true}"
                                                                    :disabled-dates="fechasDeshabilitadas"
                                                                    :class="{'is-invalid': errors.has('fecha')}"
                                                        ></datepicker>
                                                  <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>

                                            </div>
                                        </div>
                                </div>

                                    <div class="row ">
                                         <div class="col-md-2">
                                            <div class="form-group  error-content">
                                                <label for="remision" class=" col-form-label ">Remisión: </label>
                                                <div>
                                                    <input
                                                            type="text"
                                                            data-vv-as="Remisión"
                                                            v-validate="{required: true, max: 64}"
                                                            class="form-control"
                                                            name="remision"
                                                            id="remision"
                                                            placeholder="Remisión"
                                                            v-model="remision"
                                                            :class="{'is-invalid': errors.has('remision')}">
                                                    <div class="invalid-feedback" v-show="errors.has('remision')">{{ errors.first('remision') }}</div>
                                                </div>
                                            </div>
                                         </div>
                                         <div class="col-md-6">
                                             <div class="form-group error-content">
                                            <label for="id_orden_compra"  class="col-form-label">Orden de Compra: </label>
                                            <div >
                                                <model-list-select
                                                        :disabled="!bandera"
                                                        name="id_orden_compra"
                                                        placeholder="Seleccionar o buscar por número de folio o por razón social de proveedor"
                                                        data-vv-as="Orden de Compra"
                                                        v-model="id_orden_compra"
                                                        option-value="id"
                                                        :custom-text="numeroFolioAndEmpresaSucursal"
                                                        :list="ordenes_compra"
                                                >
                                                </model-list-select>
                                            </div>
                                        </div>
                                         </div>
                                        <div class="col-md-8" v-if="orden_compra.length != 0">
                                            <div class="form-group">
                                                <label for="empresa" class="col-form-label">Empresa / Sucursal: </label>
                                                <div >
                                                    <input
                                                            :disabled="true"
                                                            type="text"
                                                            data-vv-as="Empresa"
                                                            class="form-control"
                                                            :name="empresa"
                                                            placeholder="Empresa"
                                                            v-model="orden_compra.empresa_sucursal"
                                                            >
                                                </div>
                                            </div>
                                     </div>
                                    </div>


 <div class="row" v-if="orden_compra.length != 0">
                                    <div  class="col-12">
                                        <hr />
                                        <label class="col-form-label col-md-12">Partidas:</label>

                                    </div>
 </div>

                                <div class="row" v-if="orden_compra.length != 0">
                                    <div  class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="index_corto">#</th>
                                                    <th class="no_parte">No. Parte</th>
                                                    <th>Descripción</th>
                                                    <th class="unidad">Unidad</th>
                                                    <th class="fecha">Fecha Entrega</th>
                                                    <th>Cantidad Pendiente</th>
                                                    <th class="cantidad_input">Cantidad Ingresada</th>
                                                    <th class="fecha">Cumplido</th>
                                                    <th class="icono"></th>
                                                    <th style="width: 200px; max-width: 200px; min-width: 200px">Destino</th>
                                                    <th style="width: 60px; max-width: 60px; min-width: 60px"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(partida, i) in partidas">
                                                        <td>{{i+1}}</td>
                                                        <td>{{partida.numero_parte}}</td>
                                                        <td>{{partida.material}}</td>
                                                        <td>{{partida.unidad}}</td>
                                                        <td >{{partida.fecha_entrega_format}}</td>
                                                        <td class="td_money">{{partida.cantidad_pendiente}}</td>
                                                        <td >
                                                                <div class="form-group error-content">
                                                                    <input
                                                                            type="number"
                                                                            step="any"
                                                                            data-vv-as="Cantidad Ingresada"
                                                                            v-validate="{min_value: 0.001, max_value:partida.cantidad_pendiente, decimal:3}"
                                                                            class="form-control"
                                                                            :name="`cantidad_ingresada[${i}]`"
                                                                            placeholder="Cantidad Ingresada"
                                                                            v-model="partida.cantidad_ingresada"
                                                                            :class="{'is-invalid': errors.has(`cantidad_ingresada[${i}]`)}"
                                                                            v-on:keyup="validaCumplimiento(partida)">
                                                                    <div class="invalid-feedback" v-show="errors.has(`cantidad_ingresada[${i}]`)">{{ errors.first(`cantidad_ingresada[${i}]`) }}</div>
                                                                </div>
                                                        </td>
                                                        <td class="text-center" >
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" :id="`cumplido${i}`" v-on:change="validaCumplimiento(partida)" v-model="partida.cumplido">
                                                                <label :for="`cumplido${i}`" class="custom-control-label" v-model="partida.cumplido"></label>
                                                            </div>
                                                        </td>
                                                        <td  v-if="partida.destino ===  ''" >
                                                            <small class="badge badge-secondary">
                                                                <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                            </small>
                                                        </td>
                                                        <td v-else >
                                                            <small class="badge badge-success" v-if="partida.destino.tipo_destino === 1" >
                                                                <i class="fa fa-stream button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                            </small>
                                                             <small class="badge badge-success" v-else="partida.destino.tipo_destino === 2" >
                                                                <i class="fa fa-boxes button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                            </small>
                                                        </td>
                                                        <td  v-if="partida.destino ===  ''" >
                                                        </td>
                                                        <td v-else >
                                                            <span v-if="partida.destino.tipo_destino === 1" style="text-decoration: underline"  :title="partida.destino.destino.path">{{partida.destino.destino.descripcion}}</span>
                                                            <span v-if="partida.destino.tipo_destino === 2">{{partida.destino.destino.descripcion}}</span>
                                                        </td>
                                                        <td>
                                                            <i class="far fa-copy button" v-on:click="copiar_destino(partida)" ></i>
                                                            <i class="fas fa-paste button" v-on:click="pegar_destino(partida)" ></i>
                                                        </td>
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
                                                    v-model="orden_compra.observaciones"
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
                                    <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 || orden_compra.length == 0">Registrar</button>
                             </div>
                        </form>
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
                                                        :disableBranchNodes="true"
                                                ></concepto-select>
                                                <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row error-content">
                                            <label for="id_almacen" class="col-sm-2 col-form-label">Activos:</label>
                                            <div class="col-sm-10">
                                                <model-list-select
                                                        name="id_almacen"
                                                        placeholder="Seleccionar o buscar descripción del almacén"
                                                        data-vv-as="Almacén"
                                                        v-model="id_almacen_temporal"
                                                        option-value="id"
                                                        option-text="descripcion"
                                                        :list="almacenes"
                                                        >
                                                </model-list-select>
                                                <div class="invalid-feedback" v-show="errors.has('id_almacen')">{{ errors.first('id_almacen') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button  type="button"  class="btn btn-secondary" v-on:click="cerrarModalDestino"><i class="fa fa-close"  ></i> Cerrar</button>
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
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from 'vue-search-select';

    export default {
        name: "entrada-almacen-create",
        components: {ConceptoSelect, Datepicker, ModelListSelect},
        data() {
            return {
                datos_store:{},
                es:es,
                fechasDeshabilitadas:{},
                fecha : '',
                fecha_hoy : '',
                id_orden_compra : '',
                ordenes_compra : [],
                orden_compra : [],
                partidas : [],
                empresa : '',
                remision : '',
                cargando: false,
                bandera : 0,
                index_temporal : '',
                id_almacen_temporal : '',
                id_concepto_temporal : '',
                almacenes : [],
                cargos: {
                    1: "Con Cargo",
                    0: "A Consignación"
                },
                contratista: {
                    empresa_contratista: '',
                    opcion:''
                },
                destino_copiado: {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                },
                destino_seleccionado: {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                },
                contratistas:[],
                id_partida_temporal : ''
            }
        },
        mounted() {
            this.getOrdenesCompra();
            this.fecha_hoy = new Date();
            this.fecha = new Date();
            this.fechasDeshabilitadas.from= new Date();
        },
        methods: {
            init() {
                this.fecha = new Date();
                this.cargando = true;
                this.id_orden_compra = '';
                this.ordenes_compra = [];
                this.orden_compra = [];
                this.remision = '';
                this.cargando = false;
                this.bandera = 0;
                this.index_temporal = '';
                this.id_almacen_temporal = '';
                this.id_concepto_temporal = '';
                this.almacenes = [];
                this.partidas = [];
            },
            numeroFolioAndEmpresaSucursal (item) {
                return `[${item.numero_folio_format}] - [${item.empresa_sucursal}]`
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getOrdenesCompra() {
                return this.$store.dispatch('almacenes/entrada-almacen/get_ordenes_compra', {
                    config: {
                        params: {
                            scope: 'disponibleEntradaAlmacen',
                            sort: 'numero_folio',
                            order: 'desc'
                        }
                    }
                }).then(ordenes_compra => {
                    this.ordenes_compra = ordenes_compra.data;
                    this.bandera = 1;
                })
            },
            getOrdenCompra() {
                this.orden_compra = []
                this.partidas = []
                this.$validator.reset();
                return this.$store.dispatch('almacenes/entrada-almacen/get_orden_compra', {
                    id: this.id_orden_compra,
                    params: {include: ['partidas']}
                })
                    .then(data => {
                        this.orden_compra = data;
                        this.partidas = data.partidas.data;
                    })
            },
            getAlmacenes() {
                this.$store.commit('cadeco/almacen/SET_ALMACENES', []);
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/index', {
                    params: {sort: 'descripcion', order: 'asc', }
                })
                    .then(data => {
                        this.almacenes = data.data
                    })
            },

            getConcepto() {
                return this.$store.dispatch('cadeco/concepto/find', {
                    id: this.destino_seleccionado.id_destino,
                    params: {
                    }
                })
                    .then(data => {
                        this.destino_seleccionado.destino = data;
                        this.seleccionarDestino();
                    })
            },

            getAlmacen() {
                return this.$store.dispatch('cadeco/almacen/find', {
                    id: this.destino_seleccionado.id_destino,
                    params: {
                    }
                })
                    .then(data => {
                        this.destino_seleccionado.destino = data;
                        this.seleccionarDestino();
                    })
            },

            getContratista() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'contratista' }
                })
                    .then(data => {
                        this.contratistas = data.data;
                    })
            },

            modalContratista(i){
                this.id_partida_temporal = i;
                if(this.partidas[this.id_partida_temporal].contratista_seleccionado == undefined || this.partidas[this.id_partida_temporal].contratista_seleccionado == ''){
                    this.contratista.empresa_contratista = '';
                    this.contratista.opcion = '';
                }else{
                    this.contratista = this.partidas[this.id_partida_temporal].contratista_seleccionado;
                }
                if(this.contratistas.length == 0){
                    this.getContratista()
                }
                this.$validator.reset();
                $(this.$refs.contratista).modal('show');
            },

            seleccionarContratista() {
                this.partidas[this.id_partida_temporal].contratista_seleccionado = this.contratista;
                this.id_partida_temporal = ''
                this.contratista = {
                    empresa_contratista: '',
                    opcion:''
                };
                $(this.$refs.contratista).modal('hide');
                this.$validator.reset();
            },

            quitarContratista(){
                this.cargando = true;
                this.partidas[this.id_partida_temporal].contratista_seleccionado  = '';
                this.id_partida_temporal = '';
                this.contratista = {
                    empresa_contratista: '',
                    opcion:''
                };

                $(this.$refs.contratista).modal('hide');
                this.$validator.reset();
                this.cargando = false;
            },

            validaCumplimiento(partida){
                var diferencia;
                diferencia = Math.abs(parseFloat(partida.cantidad_pendiente).toFixed(3) - parseFloat(partida.cantidad_ingresada).toFixed(3));
                if(diferencia<0.001)
                {
                    partida.cumplido = true;
                }
                else if(parseFloat(Math.round(partida.cantidad_ingresada).toFixed(3)) == parseFloat(0) || partida.cantidad_ingresada==='' || partida.cantidad_ingresada  === undefined){
                    partida.cumplido = false;
                }
            },

            validate() {
                var error_destino = 0;
                var item_a_guardar = 0;
                var partidas_store = [];
                this.$validator.validate().then(result => {
                    if (result) {
                        this.$data.partidas.forEach(function(element) {
                            if(!(element.cantidad_ingresada  === undefined && element.destino  === '' )){
                                if(element.cantidad_ingresada > 0 && element.destino === '')
                                {
                                    error_destino = error_destino + 1
                                }
                            }
                            if(element.cantidad_ingresada > 0)
                            {
                                item_a_guardar = item_a_guardar + 1;
                                partidas_store.push(element);
                            }
                       });
                        if(item_a_guardar <= 0)
                        {
                            swal('¡Error!', 'Debe ingresar al menos un material.', 'error')
                        }
                        else if (error_destino > 0)
                        {
                            swal('¡Error!', 'Ingrese un destino válido.', 'error');
                        }
                        else if(moment(this.fecha_hoy).format('YYYY/MM/DD') < moment(this.fecha).format('YYYY/MM/DD')){
                            swal('¡Error!', 'La fecha no puede ser mayor a la fecha actual.', 'error')
                        }
                        else {
                           this.store(partidas_store)
                        }
                    }
                });
            },

            store(partidas) {
                this.datos_store ["id_orden_compra"] = this.id_orden_compra;
                this.datos_store ["remision"] = this.remision;
                this.datos_store ["fecha"] = this.fecha;
                this.datos_store ["observaciones"] = this.orden_compra.observaciones;
                this.datos_store ["partidas"] = partidas;
                return this.$store.dispatch('almacenes/entrada-almacen/store', this.datos_store)
                    .then((data) => {
                        this.$router.push({name: 'entrada-almacen'});
                    });
            },

            salir(){
                this.$router.push({name: 'entrada-almacen'});
            },
            modalDestino(i) {
                this.index_temporal = i;
                if(this.partidas[this.index_temporal].destino == undefined || this.partidas[this.index_temporal].destino == ''){
                    this.destino_seleccionado.tipo_destino =  '';
                    this.destino_seleccionado.destino = '';
                    this.destino_seleccionado.id_destino = '';
                }else {
                    this.destino_seleccionado = this.partidas[this.index_temporal].destino;
                }

                if(this.almacenes.length == 0) {
                    this.getAlmacenes();
                }
                this.$validator.reset();
                $(this.$refs.modal_destino).modal('show');
            },
            seleccionarDestino() {
                this.partidas[this.index_temporal].destino = this.destino_seleccionado;
                this.index_temporal = '';
                this.destino_seleccionado = {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                };
                this.id_concepto_temporal = '';
                this.id_almacen_temporal = '';
                $(this.$refs.modal_destino).modal('hide');
                this.$validator.reset();
            },
            cerrarModalDestino(){
                this.id_concepto_temporal = '';
                this.id_almacen_temporal = '';
                $(this.$refs.modal_destino).modal('hide');
                this.$validator.reset();
            },
            copiar_destino(partida){
                this.destino_copiado = partida.destino;
            },
            pegar_destino(partida){
                partida.destino = {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                };
                partida.destino.id_destino = this.destino_copiado.id_destino;
                partida.destino.tipo_destino = this.destino_copiado.tipo_destino;
                partida.destino.destino = this.destino_copiado.destino;
            }
        },
        watch: {
            id_orden_compra(value){
                if(value != ''){
                    this.getOrdenCompra();
                }
            },
            id_concepto_temporal(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.id_almacen_temporal = '';
                    this.destino_seleccionado.id_destino = value;
                    this.destino_seleccionado.tipo_destino = 1;
                    this.getConcepto();
                }
            },
            id_almacen_temporal(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.id_concepto_temporal = '';
                    this.destino_seleccionado.id_destino = value;
                    this.destino_seleccionado.tipo_destino = 2;
                    this.getAlmacen();
                }
            },
        }
    }
</script>

<style scoped>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>
