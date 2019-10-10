<template>
     <span>
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
                                        <div class="invalid-feedback" v-show="errors.has('remision')">{{ errors.first('remision') }}</div>
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
                                                    type="text"
                                                    data-vv-as="Empresa"
                                                    v-validate="{required: true}"
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
                                                <tr v-for="(doc, i) in orden_compra.partidas.data">
                                                    <td>{{i+1}}</td>
                                                    <td>{{doc.material.numero_parte}}</td>
                                                    <td>{{doc.material.descripcion}}</td>
                                                    <td>{{doc.material.unidad}}</td>
                                                    <td></td>
                                                    <td>{{doc.cantidad}}</td>
                                                    <td>
                                                        <div class="col-12">
                                                            <div class="form-group error-content">
                                                                <input
                                                                        type="number"
                                                                        step="any"
                                                                        data-vv-as="Cantidad Ingresada"
                                                                        v-validate="{required: true, min_value:0.1, max_value:doc.cantidad, decimal:2}"
                                                                        class="form-control"
                                                                        :name="`cantidad_ingresada[${i}]`"
                                                                        placeholder="Cantidad Ingresada"
                                                                        v-model="doc.cantidad_ingresada"
                                                                        :class="{'is-invalid': errors.has(`cantidad_ingresada[${i}]`)}">
                                                                <div class="invalid-feedback" v-show="errors.has(`cantidad_ingresada[${i}]`)">{{ errors.first(`cantidad_ingresada[${i}]`) }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center" v-if="parseFloat(doc.cantidad_ingresada) == parseFloat(doc.cantidad)">
                                                        <small class="badge" :class="{'badge-success':parseFloat(doc.cantidad_ingresada) == parseFloat(doc.cantidad)}">
                                                            <i class="fa fa-check-circle-o" aria-hidden="true"></i> Cumplido
                                                         </small>
                                                    </td>
                                                    <td v-else></td>
                                                    <td v-if="!doc.destino">
                                                        <span>
                                                            <button v-on:click="destino(i)" class="btn btn-info btn-sm">Seleccionar Destino</button>
                                                        </span>
                                                    </td>
                                                    <td v-else>{{doc.descripcion_destino}}</td>
                                                    <td class="text-center"><input type="checkbox" :value="doc.id" v-model="doc.selected"></td>
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
         <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
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
                                        <label for="id_concepto" class="col-sm-2 col-form-label">Concepto</label>
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
                                        <label for="almacen" class="col-sm-2 col-form-label">Almacén</label>
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
                                <button class="btn btn-primary"  data-dismiss="modal" v-on:click="seleccionar">Seleccionar</button>
                         </div>
                    </form>
                </div>
            </div>
        </div>
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
                descripcion : ''
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
                this.orden_compra = [];
                return this.$store.dispatch('compras/orden-compra/find', {
                    id: this.id_orden_compra,
                    params: {
                        include: ['empresa', 'partidas.material']
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
                    id: this.destino_temporal,
                    params: {
                    }
                })
                    .then(data => {
                        this.descripcion = data;
                    })
            },

            getConcepto() {
                return this.$store.dispatch('cadeco/concepto/find', {
                    id: this.destino_temporal,
                    params: {
                    }
                })
                    .then(data => {
                        this.descripcion = data;
                    })
            },

            store() {
                return this.$store.dispatch('almacenes/entrada-almacen/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'entrada-almacen'});
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            salir(){
                this.$router.push({name: 'entrada-almacen'});
            },
            destino(i) {
                this.index_temporal = i;
                console.log(this.almacenes.length);
                if(this.almacenes.length == 0) {
                    this.getAlmacenes();
                }
                $(this.$refs.modal).modal('show');
            },
            seleccionar() {
                console.log("seleccionar",this.destino_temporal, this.index_temporal);
                if(this.destino_temporal == '')
                {
                    swal('¡Error!', 'Debe seleccionar un destino.', 'error')
                }else {
                    this.orden_compra.partidas.data[this.index_temporal].destino = this.destino_temporal;
                    this.orden_compra.partidas.data[this.index_temporal].tipo_destino = this.tipo_temporal;
                    if(this.tipo_temporal == 1){
                        this.descripcion = this.getConcepto();
                        this.orden_compra.partidas.data[this.index_temporal].descripcion_destino = this.descripcion;
                    }
                    if(this.tipo_temporal == 2){
                        this.descripcion = this.getAlmacen();
                        this.orden_compra.partidas.data[this.index_temporal].descripcion_destino = this.descripcion;
                    }
                    this.destino_temporal = '';
                    this.index_temporal = '';
                    this.id_almacen_temporal = '';
                    this.id_concepto_temporal = '';
                    this.tipo_temporal = '';
                    this.descripcion = '';
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
                if(value != ''){
                    this.id_almacen_temporal = '';
                    this.destino_temporal = value;
                    this.tipo_temporal = 1;
                }
            },
            id_almacen_temporal(value){
                if(value != ''){
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