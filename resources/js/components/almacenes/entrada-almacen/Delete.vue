<template>
    <span>
         <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
             <i class="fa fa-trash"></i>
         </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-trash"></i> ELIMINAR ENTRADA DE ALMACÉN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row" v-if="entrada">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <b>Datos de la Entrada de Almacén</b>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="table-responsive col-md-12">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <td class="bg-gray-light"><b>Empresa:</b></td>
                                                            <td class="bg-gray-light" colspan="5">{{entrada.empresa_razon_social}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bg-gray-light"><b>Folio:</b></td>
                                                            <td class="bg-gray-light">{{entrada.numero_folio_format}}</td>
                                                            <td class="bg-gray-light"><b>Fecha:</b></td>
                                                            <td class="bg-gray-light">{{entrada.fecha_format}}</td>
                                                            <td class="bg-gray-light"><b>Referencia:</b></td>
                                                            <td class="bg-gray-light">{{entrada.referencia}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bg-gray-light"><b>Orden de Compra:</b></td>
                                                            <td class="bg-gray-light">{{entrada.orden_compra_numero_folio_format}}</td>
                                                            <td class="bg-gray-light"><b>Solicitud:</b></td>
                                                            <td class="bg-gray-light">{{entrada.solicitud_numero_folio_format}}</td>
                                                            <td class="bg-gray-light"></td>
                                                            <td class="bg-gray-light"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                         <div class="row">
                                            <div class="col-12">
                                                <b>Detalle de las partidas</b>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="table-responsive col-md-12">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th class="no_parte">No. de Parte</th>
                                                            <th>Material</th>
                                                            <th>Unidad</th>
                                                            <th>Cantidad</th>
                                                            <th>Destino</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <template v-for="(partida, i) in entrada.partidas.data">
                                                        <tr>
                                                            <td>{{i+1}}</td>
                                                            <td>{{partida.material_numero_parte}}</td>
                                                            <td>{{partida.material_descripcion}}</td>
                                                            <td>{{partida.unidad}}</td>
                                                            <td>{{partida.cantidad_format}}</td>
                                                            <td v-if="partida.destino_path"
                                                                :title="`${partida.destino_path}`"><u>{{partida.destino_descripcion}}</u></td>
                                                            <td v-else>{{partida.destino_descripcion}}</td>
                                                        </tr>
                                                        <tr v-if="partida.contratista">
                                                            <td colspan="2">
                                                                <span v-if="partida.contratista.con_cargo == 0">
                                                                    <i class="fa fa-user-o" aria-hidden="true"></i>
                                                                    Sin Cargo a:
                                                                </span>
                                                                <span v-else>
                                                                    <i class="fa fa-user" aria-hidden="true"></i>
                                                                    Con cargo a:
                                                                </span>
                                                            </td>
                                                            <td colspan="5">
                                                                {{partida.contratista.empresa.razon_social}}
                                                            </td>
                                                        </tr>
                                                    </template>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <b>Observaciones:</b>
                                            </div>
                                            <div class="col-sm-10">
                                               {{entrada.observaciones}}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div v-if="entrada.transacciones_relacionadas" class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label><i class="fas fa-clone" style="padding-right:3px"></i>Transacciones Relacionadas:</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th class="bg-gray-light index_corto">#</th>
                                                            <th class="bg-gray-light fecha_hora">Tipo</th>
                                                            <th class="bg-gray-light fecha">Folio</th>
                                                            <th class="bg-gray-light fecha">Fecha</th>
                                                            <th class="bg-gray-light fecha_hora">Fecha/Hora Registro</th>
                                                            <th class="bg-gray-light">Concepto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(transaccion, i) in entrada.transacciones_relacionadas">
                                                            <td>{{i+1}}</td>
                                                            <td>{{transaccion.tipo_transaccion}}</td>
                                                            <td>{{transaccion.numero_folio}}</td>
                                                            <td>{{transaccion.fecha}}</td>
                                                            <td>{{transaccion.fecha_hora_registro}}</td>
                                                            <td>{{transaccion.concepto}}</td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="invoice p-3 mb-3" style="width:100%">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="motivo">Motivo de eliminación:</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group row error-content">

                                                    <div class="col-md-12">
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0 || motivo == ''">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "entrada-almacen-delete",
        props: ['id', 'pagina'],
        data() {
            return {
                motivo: '',
                partidas: ''
            }
        },
        methods: {
            find() {
                this.motivo = '';
                this.partidas = '';
                this.$store.commit('almacenes/entrada-almacen/SET_ENTRADA', null);
                return this.$store.dispatch('almacenes/entrada-almacen/find', {
                    id: this.id,
                    params: {include: ['partidas', 'partidas.contratista','transacciones_relacionadas']}
                }).then(data => {
                    this.$store.commit('almacenes/entrada-almacen/SET_ENTRADA', data);
                    this.partidas = this.entrada.partidas.data;
                    $(this.$refs.modal).modal('show');
                })
            },
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('almacenes/entrada-almacen/eliminar', {
                    id: this.id,
                    params: {data: [this.$data.motivo]}
                })
                    .then(data => {
                        this.$store.commit('almacenes/entrada-almacen/DELETE_ENTRADA', {id: this.id})
                        $(this.$refs.modal).modal('hide');
                        this.$store.dispatch('almacenes/entrada-almacen/paginate', {
                            params: {
                                include: 'empresa', sort: 'numero_folio', order: 'desc', limit: 10, offset: this.pagina
                            }
                        })
                            .then(data => {
                                this.$store.commit('almacenes/entrada-almacen/SET_ENTRADAS', data.data);
                                this.$store.commit('almacenes/entrada-almacen/SET_META', data.meta);
                            })
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if (this.motivo == '') {
                            swal('¡Error!', 'Debe colocar un motivo para realizar la operación.', 'error')
                        } else {
                            this.eliminar()
                        }
                    }
                });
            },
        },
        computed: {
            entrada() {
                return this.$store.getters['almacenes/entrada-almacen/currentEntrada'];
            }
        }
    }
</script>

<style scoped>

</style>