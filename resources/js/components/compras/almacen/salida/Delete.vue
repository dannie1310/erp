<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="Rechazar">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-trash"></i> ELIMINAR ENTRADA A ALMACÉN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <form role="form" @submit.prevent="validate">
                    <div class="modal-body">
                        <div class="row"  v-if="salida">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5>Datos de la Salida Almacén</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Folio:</b></td>
                                                        <td class="bg-gray-light">{{salida.folio_format}}</td>
                                                        <td class="bg-gray-light"><b>Almacén:</b></td>
                                                        <td class="bg-gray-light">{{salida.almacen.descripcion}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Referencia:</b></td>
                                                        <td class="bg-gray-light">{{salida.referencia}}</td>
                                                        <td class="bg-gray-light"><b>Observaciones:</b></td>
                                                        <td class="bg-gray-light">{{salida.observaciones}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                                        <td class="bg-gray-light">{{salida.fecha_format}}</td>
                                                        <td class="bg-gray-light"><b>Estado:</b></td>
                                                        <td class="bg-gray-light">{{salida.estado_format}}</td>
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
                                                            <th>Material</th>
                                                            <th v-if="salida.opciones == 65537">Almacén</th>
                                                            <th>Cantidad</th>
                                                            <th v-if="salida.opciones == 1">Cantidad en Movimiento</th>
                                                            <th>Cantidad en Inventario</th>
                                                            <th>Saldo en Inventario</th>
                                                            <th>Unidad</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody v-if="salida.opciones == 65537">
                                                        <tr v-for="(doc, i) in salida.partidas.data">
                                                            <td>{{i+1}}</td>
                                                            <td>{{doc.material.descripcion}}</td>
                                                            <td>{{doc.almacen.descripcion}}</td>
                                                            <td>{{doc.cantidad}}</td>
                                                            <td v-if="doc.inventario">{{doc.inventario.cantidad_format}}</td>
                                                            <td class="text-danger"  v-else>No se encuentra ningun inventario</td>
                                                            <td v-if="doc.inventario">{{doc.inventario.saldo}}</td>
                                                            <td class="text-danger"  v-else>No se encuentra ningun inventario</td>
                                                            <td>{{doc.unidad}}</td>
                                                        </tr>
                                                    </tbody>
                                                    <tbody v-else-if="salida.opciones == 1">
                                                        <tr v-for="(doc, i) in salida.partidas.data">
                                                            <td>{{i+1}}</td>
                                                            <td v-if="doc.material">{{doc.material.descripcion}}</td>
                                                            <td class="text-danger"  v-else>No se encuentra ningun material</td>
                                                            <td>{{doc.cantidad_format}}</td>
                                                            <td v-if="doc.movimiento">{{doc.movimiento.cantidad_format}}</td>
                                                            <td v-if="doc.movimiento.inventario">{{doc.movimiento.inventario.cantidad_format}}</td>
                                                            <td class="text-danger"  v-else>No se encuentra ningun inventario</td>
                                                            <td v-if="doc.movimiento.inventario">{{doc.movimiento.inventario.saldo_format}}</td>
                                                            <td class="text-danger"  v-else>No se encuentra ningun inventario</td>
                                                            <td>{{doc.unidad}}</td>
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
                motivo: ''
            }
        },
        methods: {
            find() {
                this.$store.commit('compras/salida-almacen/SET_SALIDA', null);
                return this.$store.dispatch('compras/salida-almacen/find', {
                    id: this.id,
                    params: {include: ['almacen','partidas.movimiento.inventario','partidas.inventario','partidas.almacen','partidas.material']}
                }).then(data => {
                    this.$store.commit('compras/salida-almacen/SET_SALIDA', data);
                    $(this.$refs.modal).modal('show');
                })
            },

            eliminar() {
                return this.$store.dispatch('compras/salida-almacen/eliminar', {
                    id: this.id,
                    params: {data: [this.$data.motivo]}
                })
                    .then(data => {
                        $(this.$refs.modal).modal('hide');
                    })
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