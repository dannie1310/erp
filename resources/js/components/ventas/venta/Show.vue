<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="visualizar">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header" v-if="venta">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-paper-plane"></i>&nbsp; VENTA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="cargando">
                         <div>
                             <h5  id="exampleModalLongTitle"><i class="fa fa-spin fa-spinner"></i>CARGANDO</h5>
                         </div>
                    </div>
                    <div class="modal-body" v-else>
                        <div class="row" v-if="venta">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <b>Datos de la Venta</b>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Folio:</b></td>
                                                        <td class="bg-gray-light">{{venta.folio_format}}</td>
                                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                                        <td class="bg-gray-light">{{venta.fecha_format}}</td>

                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Referencia:</b></td>
                                                        <td class="bg-gray-light">{{venta.referencia}}</td>
                                                        <td class="bg-gray-light"><b>Almacén:</b></td>
                                                        <td class="bg-gray-light">{{venta.almacen_descripcion}}</td>
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
                                                        <th>No. de Parte</th>
                                                        <th>Material</th>
                                                        <th>Unidad</th>
                                                        <th>Cantidad</th>
                                                        <th>Precio/U</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(partida, i) in venta.partidas.data">
                                                        <td>{{i+1}}</td>
                                                        <td >{{partida.material_numero_parte}}</td>
                                                        <td >{{partida.material_descripcion}}</td>
                                                        <td>{{partida.unidad}}</td>
                                                        <td>{{partida.cantidad_format}}</td>
                                                        <td>{{partida.destino_descripcion}}</td>
                                                    </tr>
                                                    <tr v-if="venta.observaciones" class="invoice p-3 mb-3">
                                                        <td colspan="6"><b>Observaciones: </b>{{venta.observaciones}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="venta">
                            <div class="col-md-12">
<!--                                <div  v-if="venta.transacciones_relacionadas"  class="card">-->
<!--                                    <div class="card-header">-->
<!--                                        <div class="row">-->
<!--                                            <div class="col-md-12">-->
<!--                                                <label ><i class="fas fa-clone" style="padding-right:3px"></i>Transacciones Relacionadas:</label>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="card-body">-->
<!--                                        <div class="row">-->
<!--                                            <div class="col-12 table-responsive">-->
<!--                                                <table class="table table-striped">-->
<!--                                                    <thead>-->
<!--                                                        <tr>-->
<!--                                                            <th class="bg-gray-light index_corto">#</th>-->
<!--                                                            <th class="bg-gray-light fecha_hora">Tipo</th>-->
<!--                                                            <th class="bg-gray-light fecha">Folio</th>-->
<!--                                                            <th class="bg-gray-light fecha">Fecha</th>-->
<!--                                                            <th class="bg-gray-light fecha_hora">Fecha/Hora Registro</th>-->
<!--                                                            <th class="bg-gray-light">Concepto</th>-->
<!--                                                        </tr>-->
<!--                                                    </thead>-->
<!--                                                    <tbody>-->
<!--                                                        <tr v-for="(transaccion, i) in venta.transacciones_relacionadas">-->
<!--                                                            <td >{{i+1}}</td>-->
<!--                                                            <td >{{transaccion.tipo_transaccion}}</td>-->
<!--                                                            <td >{{transaccion.numero_folio}}</td>-->
<!--                                                            <td >{{transaccion.fecha}}</td>-->
<!--                                                            <td >{{transaccion.fecha_hora_registro}}</td>-->
<!--                                                            <td >{{transaccion.concepto}}</td>-->
<!--                                                        </tr>-->
<!--                                                    </tbody>-->
<!--                                                </table>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

    </span>
</template>

<script>
    export default {
        name: "venta-show",
        props: ['id','pagina'],
        data() {
            return {
                data: [],
                motivo: '',
                indice: '',
                cargo: '',
                cargando: false,
            }
        },
        methods: {
            find() {
                $(this.$refs.modal).modal('show');
                this.cargando = true;
                this.motivo = '';
                this.$store.commit('almacenes/salida-almacen/SET_SALIDA', null);
                return this.$store.dispatch('almacenes/salida-almacen/find', {
                    id: 46734,
                    params: {include: ['partidas', 'entrega_contratista', 'transacciones_relacionadas']}
                }).then(data => {
                    this.$store.commit('almacenes/salida-almacen/SET_SALIDA', data);
                }).finally(() => {
                    this.cargando = false;
                })
            },
        },
        computed: {
            venta() {
                // console.log('Salida','jorge');
                return this.$store.getters['almacenes/salida-almacen/currentSalida'];
            }
        }
    }
</script>
