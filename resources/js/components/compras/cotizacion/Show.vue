<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-secondary" v-show="show" :disabled="cargando" title="Ver Cotización">
            <i class="fa fa-eye" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> DETALLES DE LA COTIZACIÓN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="cotizacion">
                        <div class="row">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row col-md-12">
                                        <div class="col-md-6">
                                            <h5>Folio: &nbsp; <b>{{cotizacion.folio_format}}</b></h5>
                                        </div>
                                    </div>
                                    <div class="table-responsive col-md-12">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td class="bg-gray-light" align="center" colspan="4"><b>{{(cotizacion.empresa) ? cotizacion.empresa.razon_social : '----- Proveedor Desconocido -----'}}</b></td>
                                                    <!-- <td class="bg-gray-light">20/02/2020</td>
                                                    <td class="bg-gray-light"><b>Fecha Requisición Origen:</b></td>
                                                    <td class="bg-gray-light">20/02/2020</td>
                                                    <td class="bg-gray-light"><b>Folio Requisición Origen:</b></td>
                                                    <td class="bg-gray-light">20/02/2020</td> -->
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Sucursal:</b></td>
                                                    <td class="bg-gray-light">{{(cotizacion.sucursal) ? cotizacion.sucursal.descripcion : '------ Sin Sucursal ------'}}</td>
                                                    <td class="bg-gray-light"><b>Fecha:</b></td>
                                                    <td class="bg-gray-light">{{cotizacion.fecha_format}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Direccion:</b></td>
                                                    <td class="bg-gray-light">{{(cotizacion.sucursal) ? cotizacion.sucursal.direccion : '--------------------'}}</td>
                                                    <td class="bg-gray-light"><b>Usuario Registró:</b></td>
                                                    <td class="bg-gray-light">Usuario</td></tr>
                                            </tbody>
                                        </table>
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
                                                        <th class="no_parte">Núm de Parte</th>
                                                        <th>Descripción</th>
                                                        <th class="no_parte">Cantidad</th>
                                                        <th class="no_parte">Fecha Entrega</th>
                                                        <th>Destino</th>
                                                        <th>Observaciones</th>
                                                    </tr>
                                                </thead>
                                                <!-- <tbody>
                                                    <tr v-for="(partida, i) in solicitud.partidas.data">
                                                        <td>{{i+1}}</td>
                                                        <td style="text-align: center"><b>{{partida.material.numero_parte}}</b></td>
                                                        <td style="text-align: center">{{partida.material.descripcion}}</td>
                                                        <td style="text-align: center">{{partida.cantidad}}</td>
                                                        <td style="text-align: center">{{(partida.entrega) ? partida.entrega.fecha_format : '------------'}}</td>
                                                        <td v-if="partida.entrega">{{(partida.entrega.concepto) ? partida.entrega.concepto.path : partida.entrega.almacen ? partida.entrega.almacen.descripcion : '------------'}}</td>
                                                        <td style="text-align: left">{{(partida.complemento) ? partida.complemento.observaciones : '------------'}}</td>
                                                    </tr>
                                                </tbody> -->
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row col-md-12">
                                        <div class="col-md-2"><b>Observaciones:</b></div>
                                        <div class="col-md-10">Observaciones</div>
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
        name: "cotizacion-show",
        props: ['id', 'show'],
        data(){
            return{
                cargando: false,
            }
        },
        methods: {
            find() {

                this.cargando = true;
                this.$store.commit('compras/cotizacion/SET_COTIZACION', null);
                return this.$store.dispatch('compras/cotizacion/find', {
                    id: this.id,
                    params:{include: ['empresa', 'sucursal']}
                }).then(data => {
                    this.$store.commit('compras/cotizacion/SET_COTIZACION', data);

                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                    this.cargando = false;
                    
                })
            }
        },
        computed: {
            cotizacion() {
                return this.$store.getters['compras/cotizacion/currentCotizacion']
            }
        }
    }
</script>

<style scoped>

</style>
