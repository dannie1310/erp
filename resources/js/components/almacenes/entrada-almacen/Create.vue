<template>
     <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <div class="row">
                            <div class="col-12">
                                <h4>
                                    <i class="fa fa-list"></i> Registrar Entrada de Almacén
                                </h4>
                            </div>
                        </div>
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row justify-content-end">
                                     <div class="col-4">
                                        <div class="form-group row error-content">
                                            <label for="remision">Remisión: </label>
                                            <input
                                                    type="text"
                                                    data-vv-as="Remisión"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    :name="remision"
                                                    placeholder="Remisión"
                                                    v-model="remision"
                                                    :class="{'is-invalid': errors.has('remision')}">
                                            <div class="error-label" v-show="errors.has('remision')">{{ errors.first('remision') }}</div>
                                        </div>
                                     </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <label for="id_orden_compra"  class="col-sm-2 col-form-label">Orden de Compra: </label>
                                            <div class="col-sm-10">
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
                                                    <option value>-- Seleccione una Orden de Compra --</option>
                                                    <option v-for="orden in ordenes_compra" :value="orden.id">{{ orden.numero_folio_format }} ({{ orden.dato_transaccion }})</option>
                                                </select>
                                                <div class="error-label" v-show="errors.has('id_orden_compra')">{{ errors.first('id_orden_compra') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row"  v-if="id_orden_compra != '' && orden_compra.empresa">
                                     <div class="col-12">
                                        <div class="form-group row error-content">
                                            <label for="empresa" class="col-sm-2 col-form-label">Empresa: </label>
                                            <div class="col-sm-10">
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
                                        <div class="table-responsive">
                                            <table class="table table-striped">
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
                                                    <th>Entrega a Contratista</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(doc, i) in partidas">
                                                        <td v-if="doc.cantidad_pendiente != 0">{{i+1}}</td>
                                                        <td v-if="doc.cantidad_pendiente != 0">{{doc.material.numero_parte}}</td>
                                                        <td v-if="doc.cantidad_pendiente != 0">{{doc.material.descripcion}}</td>
                                                        <td v-if="doc.cantidad_pendiente != 0">{{doc.material.unidad}}</td>
                                                        <td v-if="doc.cantidad_pendiente != 0"></td>
                                                        <td v-if="doc.cantidad_pendiente != 0">{{doc.cantidad_pendiente}}</td>
                                                        <td v-if="doc.cantidad_pendiente != 0">
                                                            <div class="col-12">
                                                                <div class="form-group error-content">
                                                                    <input
                                                                            type="number"
                                                                            step="any"
                                                                            data-vv-as="Cantidad Ingresada"
                                                                            v-validate="{min_value:0.1, max_value:doc.cantidad_pendiente, decimal:2}"
                                                                            class="form-control"
                                                                            :name="`cantidad_ingresada[${i}]`"
                                                                            placeholder="Cantidad Ingresada"
                                                                            v-model="doc.cantidad_ingresada"
                                                                            :class="{'is-invalid': errors.has(`cantidad_ingresada[${i}]`)}">
                                                                    <div class="invalid-feedback" v-show="errors.has(`cantidad_ingresada[${i}]`)">{{ errors.first(`cantidad_ingresada[${i}]`) }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center" v-if="doc.cantidad_pendiente != 0 && parseFloat(doc.cantidad_ingresada) == parseFloat(doc.cantidad_pendiente)">
                                                            <small class="badge" :class="{'badge-success':parseFloat(doc.cantidad_ingresada) == parseFloat(doc.cantidad_pendiente)}">
                                                                <i class="fa fa-check-circle-o" aria-hidden="true"></i> Cumplido
                                                             </small>
                                                        </td>
                                                        <td v-else-if="doc.cantidad_pendiente != 0"></td>
                                                        <td v-if="doc.cantidad_pendiente != 0">
                                                            <small class="badge" :class="{'badge-success':true}">
                                                                <i class="fa fa-sign-in" aria-hidden="true" v-on:click="destino(i)"></i>
                                                            </small>
                                                            <label v-if = "doc.tipo_destino == 2" v-model="doc.destino">{{doc.descripcion_destino.descripcion}}</label>
                                                            <label v-else-if="doc.tipo_destino == 1" v-model="doc.destino">{{doc.descripcion_destino.path}}</label>
                                                        </td>
                                                        <!--<td v-else>{{doc.descripcion_destino}}</td>-->
                                                        <td class="text-center" v-if="doc.cantidad_pendiente != 0"><i class="fa fa-user-o" aria-hidden="true" v-on:click="modalContratista(i)"></i>{{doc.contratista}}</td>
                                                        <td v-else></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <label for="observaciones" class="col-sm-2 col-form-label">Observaciones: </label>
                                            <div class="col-sm-10">
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
                            </div>
                            <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                                    <button type="submit" class="btn btn-primary":disabled="errors.count() > 0">Registrar</button>
                             </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
        <nav>
            <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> Selecciona un Destino:</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form" @submit.prevent="seleccionar">
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
                                    <button  class="btn btn-primary" data-dismiss="modal" v-on:click="seleccionar">Seleccionar</button>
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
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> AGREGAR CONTRATISTA</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                         <form role="form">
                            <div class="modal-body">
                                <fieldset class="form-group">
                                    <div class="row"  v-if="contratistas">
                                          <div class="col-md-8">
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
                                         <div class="col-md-6">
                                            <div class="form-group row error-content">
                                                <label for="opcion" class="col-sm-3 col-form-label">Tipo: </label>
                                                <div class="col-sm-10">
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
                                <button type="button" class="btn btn-danger" @click="quitarContratista">Quitar Contratista</button>
                                <button type="button" class="btn btn-primary" :disabled="errors.count() > 0 || contratista.empresa_contratista == ''" @click="seleccionarContratista">Registrar Contratista</button>
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
    export default {
        name: "entrada-almacen-create",
        components: {ConceptoSelect},
        data() {
            return {
                id_orden_compra : '',
                ordenes_compra : [],
                orden_compra : [],
                partidas : [],
                empresa : '',
                remision : '',
                cargando: false,
                bandera : 0,
                destino_temporal : '',
                index_temporal : '',
                tipo_temporal : '',
                id_almacen_temporal : '',
                id_concepto_temporal : '',
                almacenes : [],
                descripcion_temporal : [],
                cargos: {
                    1: "Con Cargo",
                    0: "Sin Cargo"
                },
                contratista: {
                    empresa_contratista: '',
                    opcion:''
                },
                contratistas:[],
                datos_extra: {},
                id_partida_temporal : '',
                contratista_use : ''
            }
        },
        mounted() {
            this.getOrdenesCompra();
        },
        methods: {
            init() {
                this.cargando = true;
                this.id_orden_compra = '';
                this.ordenes_compra = [];
                this.orden_compra = [];
                this.remision = '';
                this.cargando = false;
                this.bandera = 0;
                this.destino_temporal = '';
                this.index_temporal = '';
                this.tipo_temporal = '';
                this.id_almacen_temporal = '';
                this.id_concepto_temporal = '';
                this.almacenes = [];
                this.descripcion_temporal = [];
                this.partidas = [];
            },
            getOrdenesCompra() {
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
                return this.$store.dispatch('compras/orden-compra/find', {
                    id: this.id_orden_compra,
                    params: {
                        include: ['empresa', 'partidas.material']
                    }
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

                })
                    .then(data => {
                        this.almacenes = data.data
                    })
            },

            getAlmacen() {
                return this.$store.dispatch('cadeco/almacen/find', {
                    id: this.destino_temporal,
                    params: {
                    }
                })
                    .then(data => {
                        this.partidas[this.index_temporal].descripcion_destino = data;
                    })
            },

            getConcepto() {
                return this.$store.dispatch('cadeco/concepto/find', {
                    id: this.destino_temporal,
                    params: {
                    }
                })
                    .then(data => {
                        this.partidas[this.index_temporal].descripcion_destino = data;
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

            findContratista() {
                this.$store.commit('cadeco/empresa/SET_EMPRESA', null);
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: this.contratista.empresa_contratista,
                    params: {}
                }).then(data => {
                    this.emp_cont = data;
                })
            },

            store() {
                return this.$store.dispatch('almacenes/entrada-almacen/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'entrada-almacen'});
                    });
            },

            modalContratista(i){
                console.log("valor index", i, this.contratista)
                this.id_partida_temporal = i;
                if(this.partidas[this.id_partida_temporal].contratista_seleccionado == undefined || this.partidas[this.id_partida_temporal].contratista_seleccionado == ''){
                    console.log("vaciar?", this.id_partida_temporal, i)
                    this.contratista.empresa_contratista = '';
                    this.contratista.opcion = '';
                }else{
                    console.log("con datos: ", this.id_partida_temporal, i, this.partidas[this.id_partida_temporal].contratista_seleccionado, this.contratista)
                    this.contratista = this.partidas[this.id_partida_temporal].contratista_seleccionado;
                }
                if(this.contratistas.length == 0){
                    this.getContratista()
                }
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
                this.cargando = false;
            },

            validate() {
                if(this.contratista.empresa_contratista != '' || this.contratista.opcion != '') {
                    console.log("AQUUI?? djkfhf")
                    this.findContratista();
                }
                this.$validator.validate().then(result => {
                    if (result) {
                        // if(this.destino_temporal == '')
                        // {
                        //     swal('¡Error!', 'Debe seleccionar un destino.', 'error')
                        // }else {
                            this.store()
                        //}
                    }
                });
            },
            salir(){
                this.$router.push({name: 'entrada-almacen'});
            },
            destino(i) {
                 this.destino_temporal = '';
                 this.index_temporal = '';
                 this.id_almacen_temporal = '';
                 this.id_concepto_temporal = '';
                 this.tipo_temporal = '';
                 this.descripcion = '';
                this.index_temporal = i;
                if(this.almacenes.length == 0) {
                    this.getAlmacenes();
                }
                $(this.$refs.modal).modal('show');
            },
            seleccionar() {
                this.partidas[this.index_temporal].destino = this.destino_temporal;
                this.partidas[this.index_temporal].tipo_destino = this.tipo_temporal;

                if (this.tipo_temporal == 1) {
                    this.getConcepto();
                }
                if (this.tipo_temporal == 2) {
                    this.getAlmacen();
                }
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
                    this.destino_temporal = value;
                    this.tipo_temporal = 1;
                }
            },
            id_almacen_temporal(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.id_concepto_temporal = '';
                    this.destino_temporal = value;
                    this.tipo_temporal = 2;
                }
            }
        }
    }
</script>

<style scoped>

</style>