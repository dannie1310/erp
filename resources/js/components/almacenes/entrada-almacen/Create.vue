<template>
     <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row">


                                        <div class="offset-md-10 col-md-2">
                                            <div class="form-group error-content">
                                                <label for="fecha" class="col-form-label">Fecha:</label>
                                                    <datepicker v-model = "fecha"
                                                                    name = "fecha"
                                                                    :format = "formatoFecha"
                                                                    :language = "es"
                                                                    :bootstrap-styling = "true"
                                                                    :use-utc="true"
                                                                    class = "form-control"
                                                                    v-validate="{required: true}"
                                                                    :class="{'is-invalid': errors.has('fecha')}"
                                                        ></datepicker>
                                                  <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                            </div>
                                        </div>
                                </div>

                                    <div class="row ">
                                         <div class="col-md-2">
                                            <div class="form-group row error-content">
                                                <label for="remision" class=" col-form-label col-md-6">Remisión: </label>
                                                <div class="col-md-6" >
                                                    <input
                                                            type="text"
                                                            data-vv-as="Remisión"
                                                            v-validate="{required: true}"
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
                                         <div class="col-md-2">
                                             <div class="form-group row error-content">
                                            <label for="id_orden_compra"  class="col-form-label col-md-6">Orden de Compra: </label>
                                            <div class="col-md-6" >
                                                <select
                                                        :disabled="!bandera"
                                                        type="text"
                                                        name="id_orden_compra"
                                                        data-vv-as="Orden de Compra"
                                                        v-validate="{required: true}"
                                                        class="form-control"
                                                        id="id_orden_compra"
                                                        v-model="id_orden_compra"
                                                        :class="{'is-invalid': errors.has('id_orden_compra')}"
                                                >
                                                    <option value v-if="bandera">-- Seleccione --</option>
                                                    <option value v-if="!bandera">Cargando...</option>
                                                    <option v-for="orden in ordenes_compra" :value="orden.id">{{ orden.numero_folio_format }} </option>
                                                </select>
                                                <div class="error-label" v-show="errors.has('id_orden_compra')">{{ errors.first('id_orden_compra') }}</div>
                                            </div>
                                        </div>
                                         </div>
                                        <div class="col-md-8" v-if="orden_compra.length != 0">
                                            <div class="form-group row error-content">
                                                <label for="empresa" class="col-md-2 col-form-label">Empresa: </label>
                                                <div class="col-md-10">
                                                    <input
                                                            :disabled="true"
                                                            type="text"
                                                            data-vv-as="Empresa"
                                                            class="form-control"
                                                            :name="empresa"
                                                            placeholder="Empresa"
                                                            v-model="orden_compra.empresa.razon_social"
                                                            :class="{'is-invalid': errors.has('empresa')}">
                                                    <div class="invalid-feedback" v-show="errors.has('empresa')">{{ errors.first('empresa') }}</div>
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
                                                    <th>#</th>
                                                    <th>No. de Parte</th>
                                                    <th>Descripción</th>
                                                    <th>Unidad</th>
                                                    <th>Fecha Entrega</th>
                                                    <th>Cantidad Pendiente</th>
                                                    <th>Cantidad Ingresada</th>
                                                    <th>Cumplido</th>
                                                    <th>Destino</th>
                                                    <th class="th_icono"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(doc, i) in partidas[0]">
                                                        <td>{{i+1}}</td>
                                                        <td>{{doc.material.numero_parte}}</td>
                                                        <td>{{doc.material.descripcion}}</td>
                                                        <td>{{doc.material.unidad}}</td>
                                                        <td class="fecha">{{doc.entrega.fecha_format}}</td>
                                                        <td class="td_money">{{doc.entrega.pendiente}}</td>
                                                        <td class="td_money_input">
                                                                <div class="form-group error-content">
                                                                    <input
                                                                            type="number"
                                                                            step="any"
                                                                            data-vv-as="Cantidad Ingresada"
                                                                            v-validate="{min_value: 0.01, max_value:doc.entrega.pendiente, decimal:2}"
                                                                            class="form-control"
                                                                            :name="`cantidad_ingresada[${i}]`"
                                                                            placeholder="Cantidad Ingresada"
                                                                            v-model="doc.cantidad_ingresada"
                                                                            :class="{'is-invalid': errors.has(`cantidad_ingresada[${i}]`)}">
                                                                    <div class="invalid-feedback" v-show="errors.has(`cantidad_ingresada[${i}]`)">{{ errors.first(`cantidad_ingresada[${i}]`) }}</div>
                                                                </div>
                                                        </td>
                                                        <td class="text-center" >
                                                             <i class="fa fa-check-square-o" style="font-size: 1.2em;" v-if="parseFloat(doc.cantidad_ingresada) == parseFloat(doc.entrega.pendiente)"></i>
                                                             <i class="fa fa-square-o" style="font-size:1.2em;" v-else></i>
                                                        </td>

                                                        <td v-if="doc.destino ===  undefined">
                                                            <small class="badge" :class="{'badge-success':true}">
                                                                <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="destino(i)" ></i>
                                                            </small>
                                                        </td>
                                                        <td v-if="doc.destino">
                                                            <small class="badge" :class="{'badge-success':true}">
                                                                <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="destino(i)" ></i>
                                                            </small>
                                                            <label v-if="doc.destino.tipo_destino === 1" style="text-decoration: underline"  :title="doc.destino.destino.path">{{doc.destino.destino.descripcion}}</label>
                                                            <label v-if="doc.destino.tipo_destino === 2">{{doc.destino.destino.descripcion}}</label>
                                                        </td>
                                                        <!--<td v-else>{{doc.descripcion_destino}}</td>-->
                                                        <td class="text-center" v-if="(doc.contratista_seleccionado === undefined || doc.contratista_seleccionado === '' )">
                                                            <i class="fa fa-user-o button" aria-hidden="true" v-on:click="modalContratista(i)" ></i>{{doc.contratista}}
                                                        </td>
                                                        <td class="text-center" v-else-if="doc.contratista_seleccionado != ''">
                                                            <i class="fa fa-user button" aria-hidden="true" v-on:click="modalContratista(i)" ></i>
                                                        </td>
                                                        <!--<td v-else></td>-->
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
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> Selecciona un Destino:</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row error-content">
                                            <label for="id_concepto" class="col-sm-2 col-form-label">Conceptos</label>
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
                                            <label for="almacen" class="col-sm-2 col-form-label">Activos</label>
                                            <div class="col-sm-10">
                                                <select
                                                        name="id_almacen"
                                                        id="id_almacen"
                                                        data-vv-as="Almacén"
                                                        class="form-control"
                                                        v-model="id_almacen_temporal"
                                                        :class="{'is-invalid': errors.has('id_almacen')}"
                                                >
                                                    <option value>-- Almacén --</option>
                                                    <option v-for="item in almacenes" :value="item.id">{{ item.descripcion }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-show="errors.has('id_almacen')">{{ errors.first('id_almacen') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                    <button  type="button"  class="btn btn-primary" v-on:click="seleccionar">Seleccionar</button>
                             </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
        <nav>
            <div class="modal fade" ref="contratista" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> SELECCIONAR UN CONTRATISTA</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                         <form role="form">
                            <div class="modal-body">
                                <fieldset class="form-group">
                                    <div class="row"  v-if="contratistas">
                                          <div class="col-md-12">
                                            <div class="form-group error-content">
                                                <label for="empresa_contratista">Empresa Contratista:</label>
                                                   <select
                                                           class="form-control"
                                                           name="empresa_contratista"
                                                           data-vv-as="Contratsta"
                                                           v-model="contratista.empresa_contratista"
                                                           v-validate="{required: false}"
                                                           id="empresa_contratista"
                                                           :class="{'is-invalid': errors.has('empresa_contratista')}">.
                                                    <option value>-- Seleccione --</option>
                                                    <option v-for="contratista in contratistas" :value="contratista.id">{{ contratista.razon_social }} </option>
                                                </select>
                                                 <div class="invalid-feedback" v-show="errors.has('empresa_contratista')">{{ errors.first('empresa_contratista') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                         <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                <div class="col-sm-12">
                                                    <div class="btn-group btn-group-toggle">
                                                        <label class="btn btn-outline-secondary" :class="contratista.opcion === Number(key) ? 'active': ''" v-for="(cargo, key) in cargos" :key="key">
                                                            <input type="radio"
                                                                   class="btn-group-toggle"
                                                                   name="opcion"
                                                                   :id="'opcion' + key"
                                                                   :value="key"
                                                                   autocomplete="on"
                                                                   v-validate="{required: false}"
                                                                   v-model.number="contratista.opcion">
                                                                {{ cargo }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                             <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-danger" @click="quitarContratista">Quitar Selección</button>
                                <button type="button" class="btn btn-primary" :disabled="errors.count() > 0 || contratista.empresa_contratista == '' || contratista.opcion === ''" @click="seleccionarContratista">Seleccionar</button>
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
    export default {
        name: "entrada-almacen-create",
        components: {ConceptoSelect, Datepicker},
        data() {
            return {
                es:es,
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
                    0: "Sin Cargo"
                },
                contratista: {
                    empresa_contratista: '',
                    opcion:''
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
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getOrdenesCompra() {
                this.fecha_hoy = new Date();
                this.fecha = new Date();
                return this.$store.dispatch('compras/orden-compra/index', {
                    config: {
                        params: {
                            scope: 'disponibleEntradaAlmacen',
                            sort: 'numero_folio',
                            order: 'desc'
                        }
                    }
                }).then(data => {
                    this.ordenes_compra = data;
                    this.bandera = 1;
                })
            },
            getOrdenCompra() {
                this.orden_compra = []
                this.partidas = []
                this.$validator.reset();
                return this.$store.dispatch('compras/orden-compra/find', {
                    id: this.id_orden_compra,
                    params: {
                        include: ['empresa', 'partidas.material', 'partidas.entrega']
                    }
                })
                    .then(data => {
                        this.orden_compra = data;
                    })
            },
            getAlmacenes() {
                this.$store.commit('cadeco/almacen/SET_ALMACENES', []);
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/index', {

                })
                    .then(data => {
                        this.almacenes = data.data
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
                if(this.partidas[0][this.id_partida_temporal].contratista_seleccionado == undefined || this.partidas[0][this.id_partida_temporal].contratista_seleccionado == ''){
                    this.contratista.empresa_contratista = '';
                    this.contratista.opcion = '';
                }else{
                    this.contratista = this.partidas[0][this.id_partida_temporal].contratista_seleccionado;
                }
                if(this.contratistas.length == 0){
                    this.getContratista()
                }
                this.$validator.reset();
                $(this.$refs.contratista).modal('show');
            },

            seleccionarContratista() {
                this.partidas[0][this.id_partida_temporal].contratista_seleccionado = this.contratista;
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
                this.partidas[0][this.id_partida_temporal].contratista_seleccionado  = '';
                this.id_partida_temporal = '';
                this.contratista = {
                    empresa_contratista: '',
                    opcion:''
                };

                $(this.$refs.contratista).modal('hide');
                this.$validator.reset();
                this.cargando = false;
            },

            validate() {
                var error_destino = 0;
                var item_a_guardar = 0;
                this.$validator.validate().then(result => {
                    if (result) {
                        this.$data.partidas[0].forEach(function(element) {
                            if(!(element.cantidad_ingresada  === undefined && element.destino  === undefined )){
                                if(element.cantidad_ingresada > 0 && element.destino === undefined)
                                {
                                    error_destino = error_destino + 1
                                }
                                item_a_guardar = item_a_guardar + 1;
                            }
                       });
                        if(item_a_guardar <= 0)
                        {
                            swal('¡Error!', 'Debe registrar un material a esta entrada de almacén.', 'error')
                        }
                        else if (error_destino > 0)
                        {
                            swal('¡Error!', 'Ingrese un destino válido.', 'error');
                        }
                        else if(moment(this.fecha_hoy).format('YYYY/MM/DD') < moment(this.fecha).format('YYYY/MM/DD')){
                            swal('¡Error!', 'La fecha no puede ser mayor a la fecha actual.', 'error')
                        }
                        else {
                           this.store()
                        }
                    }
                });
            },

            store() {
                return this.$store.dispatch('almacenes/entrada-almacen/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'entrada-almacen'});
                    });
            },

            salir(){
                this.$router.push({name: 'entrada-almacen'});
            },
            destino(i) {
                this.index_temporal = i;
                if(this.partidas[0][this.index_temporal].destino == undefined || this.partidas[0][this.index_temporal].destino == ''){
                    this.destino_seleccionado.tipo_destino =  '';
                    this.destino_seleccionado.destino = '';
                    this.destino_seleccionado.id_destino = '';
                }else {
                    this.destino_seleccionado = this.partidas[0][this.index_temporal].destino;
                }

                if(this.almacenes.length == 0) {
                    this.getAlmacenes();
                }
                this.$validator.reset();
                $(this.$refs.modal_destino).modal('show');
            },
            seleccionar() {
                this.partidas[0][this.index_temporal].destino = this.destino_seleccionado;
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
            fecha(value){
                 if(value != ''){
                     if(moment(this.fecha_hoy).format('YYYY/MM/DD') < moment(value).format('YYYY/MM/DD')){
                       swal('¡Error!', 'La fecha no puede ser mayor a la fecha actual.', 'error')
                     }
                }
            },
            orden_compra(value){
                var array_limpio = [];
                if(value != ''){
                    var items =  value.partidas.data
                   items.forEach(function(element) {
                        if(element.entrega.pendiente!= 0){
                            array_limpio.push(element);
                        }
                    });
                   this.partidas.push(array_limpio)
                }
            }
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