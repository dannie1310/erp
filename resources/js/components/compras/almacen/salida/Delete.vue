<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" v-if="salida">
                        <h5 class="modal-title" id="exampleModalLongTitle" v-if="salida.opciones == 1"> <i class="fa fa-trash"></i> ELIMINAR SALIDA DE  ALMACÉN</h5>
                        <h5 class="modal-title" id="exampleModalLongTitle" v-else> <i class="fa fa-trash"></i> ELIMINAR TRANSFERENCIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <form role="form" @submit.prevent="validate">
                    <div class="modal-body">
                        <div class="row" v-if="salida">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 v-if="salida.opciones == 1">Datos de Consumo</h5>
                                            <h5 v-else>Datos de Transferencia</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Folio:</b></td>
                                                        <td class="bg-gray-light">{{salida.folio_format}}</td>
                                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                                        <td class="bg-gray-light">{{salida.fecha_format}}</td>

                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Referencia:</b></td>
                                                        <td class="bg-gray-light">{{salida.referencia}}</td>
                                                        <td class="bg-gray-light"><b>Almacén:</b></td>
                                                        <td class="bg-gray-light">{{salida.almacen.descripcion}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
                                                        <th>No. de Parte</th>
                                                        <th>Material</th>
                                                        <th>Unidad</th>
                                                        <th>Cantidad</th>
                                                        <th>Destino</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(doc, i) in salida.partidas.data">
                                                        <td>{{i+1}}</td>
                                                        <td v-if="doc.material">{{doc.material.numero_parte}}</td>
                                                        <td v-if="doc.material">{{doc.material.descripcion}}</td>
                                                        <td>{{doc.unidad}}</td>
                                                        <td>{{doc.cantidad_format}}</td>
                                                        <td v-if="salida.opciones == 1">{{doc.concepto.descripcion}}</td>
                                                        <td v-else>{{doc.almacen.descripcion}}</td>
                                                    </tr>
                                                    <tr v-if="salida.observaciones" class="invoice p-3 mb-3">
                                                        <td colspan="6"><b>Observaciones: </b>{{salida.observaciones}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                 <label for="motivo" class="col-sm-2 col-form-label">Motivo: </label>
                                                <div class="col-sm-10">
                                                    <textarea
                                                            name="motivo"
                                                            id="motivo"
                                                            class="form-control"
                                                            v-model="motivo"
                                                            v-validate="{required: true}"
                                                            data-vv-as="Motivo"
                                                            :class="{'is-invalid': errors.has('motivo')}"
                                                    ></textarea>
                                                    <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-danger":disabled="errors.count() > 0 || motivo ==''">Eliminar</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "salida-almacen-delete",
        props: ['id'],
        data() {
            return {
                data: [],
                motivo: '',
                query: {include: ['almacen'], sort: 'numero_folio', order: 'desc'}

            }
        },
        methods: {
            find() {
                this.$store.commit('compras/salida-almacen/SET_SALIDA', null);
                return this.$store.dispatch('compras/salida-almacen/find', {
                    id: this.id,
                    params: {include: ['almacen','partidas.movimiento.inventario','partidas.inventario','partidas.almacen','partidas.material','partidas.concepto']}
                }).then(data => {
                    this.$store.commit('compras/salida-almacen/SET_SALIDA', data);
                    $(this.$refs.modal).modal('show');
                })
            },

            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('compras/salida-almacen/eliminar', {
                    id: this.id,
                    params: {data: [this.$data.motivo]}
                })
                    .then(data => {
                        this.$store.commit('compras/salida-almacen/DELETE_SALIDA', {id: this.id})
                        $(this.$refs.modal).modal('hide');
                        this.$store.dispatch('compras/salida-almacen/paginate', {params: this.query})
                            .then(data => {
                                this.$store.commit('compras/salida-almacen/SET_SALIDAS', data.data);
                                this.$store.commit('compras/salida-almacen/SET_META', data.meta);
                            })
                    })
                    .finally( ()=>{
                        this.cargando = false;
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.motivo == '') {
                            swal('¡Error!', 'Debe colocar un motivo para realizar la operación.', 'error')
                        }
                        else {
                            this.eliminar()
                        }
                    }
                });
            },
        },
        computed: {
            salida() {
                return this.$store.getters['compras/salida-almacen/currentSalida'];
            }
        }
    }
</script>

<style scoped>

</style>