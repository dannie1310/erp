<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-primary" :disabled="cargando" title="Ver">
            <i style="width:40px;" v-if="!cargando">{{numero_folio}}</i>
            <i class="fa fa-spinner fa-spin" style="width:40px;" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header" v-if="salida">
                        <h5 class="modal-title" id="exampleModalLongTitle" v-if="salida.opciones == 1"> <i class="fa fa-sign-out"></i>SALIDA DE  ALMACÉN</h5>
                        <h5 class="modal-title" id="exampleModalLongTitle" v-else> <i class="fa fa-th"></i>TRANSFERENCIA DE ALMACÉN</h5>
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
                        <div class="row" v-if="salida">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <b v-if="salida.opciones == 1">Datos del Consumo</b>
                                            <b v-else>Datos de Transferencia</b>
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
                                                        <td class="bg-gray-light">{{salida.almacen_descripcion}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <template v-if="salida.entrega_contratista">
                                        <div class="row">
                                            <div class="col-12">
                                                <b>Datos de Entrega</b>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="table-responsive col-md-12">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th class="numero_folio">Folio</th>
                                                            <th >Contratista</th>
                                                            <th style="width: 80px">Tipo</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td >{{salida.entrega_contratista.folio_format}}</td>
                                                            <td >{{salida.entrega_contratista.contratista}}</td>
                                                            <td >{{salida.entrega_contratista.tipo}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </template>
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
                                                        <th>Destino</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(partida, i) in salida.partidas.data">
                                                        <td>{{i+1}}</td>
                                                        <td >{{partida.material_numero_parte}}</td>
                                                        <td >{{partida.material_descripcion}}</td>
                                                        <td>{{partida.unidad}}</td>
                                                        <td>{{partida.cantidad_format}}</td>
                                                        <td v-if="partida.destino_path" :title="`${partida.destino_path}`"><u>{{partida.destino_descripcion}}</u></td>
                                                        <td v-else >{{partida.destino_descripcion}}</td>
                                                    </tr>
                                                    <tr v-if="salida.observaciones" class="invoice p-3 mb-3">
                                                        <td colspan="6"><b>Observaciones: </b>{{salida.observaciones}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="salida">
                            <div class="col-md-12">
                                <div  v-if="salida.transacciones_relacionadas"  class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label ><i class="fas fa-clone" style="padding-right:3px"></i>Transacciones Relacionadas:</label>
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
                                                        <tr v-for="(transaccion, i) in salida.transacciones_relacionadas">
                                                            <td >{{i+1}}</td>
                                                            <td >{{transaccion.tipo_transaccion}}</td>
                                                            <td >{{transaccion.numero_folio}}</td>
                                                            <td >{{transaccion.fecha}}</td>
                                                            <td >{{transaccion.fecha_hora_registro}}</td>
                                                            <td >{{transaccion.concepto}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        name: "salida-almacen-show",
        props: ['id' , 'pagina','numero_folio'],
        data() {
            return {
                cargando: false,
            }
        },
        methods: {
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.cargando = true;
                this.$store.commit('almacenes/salida-almacen/SET_SALIDA', null);
                return this.$store.dispatch('almacenes/salida-almacen/find', {
                    id: this.id,
                    params: {include: ['partidas', 'entrega_contratista', 'transacciones_relacionadas']}
                }).then(data => {
                    this.$store.commit('almacenes/salida-almacen/SET_SALIDA', data);
                }).finally(() => {
                    this.cargando = false;
                })
            },
        },
        computed: {
            salida() {
                return this.$store.getters['almacenes/salida-almacen/currentSalida'];
            }
        }
    }
</script>
