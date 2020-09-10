<template>
    <span>
        <button @click="find()" v-if="boton" type="button" class="btn btn-sm btn-primary" :disabled="cargando" title="Ver Solicitud">
            <i style="width:40px;" v-if="!cargando">{{boton.numero_folio_format}}</i>
            <i class="fa fa-spinner fa-spin" style="width:40px;" v-else></i>
        </button>
        <button @click="find()" v-else type="button" class="btn btn-sm btn-outline-secondary" :disabled="cargando" title="Ver Solicitud">
            <i class="fa fa-eye" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> DETALLES DE LA SOLICITUD</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="solicitud">
                        <div class="row">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row col-md-12">
                                        <div class="col-md-6">
                                            <h5>Folio SAO: &nbsp; <b>{{solicitud.numero_folio_format}}</b></h5>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Folio: &nbsp; <b>{{solicitud.complemento ? solicitud.complemento.folio : '---'}}</b></h5>
                                        </div>
                                    </div>
                                    <div class="table-responsive col-md-12">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Fecha:</b></td>
                                                    <td class="bg-gray-light"> {{solicitud.fecha_format}} </td>
                                                    <td class="bg-gray-light"><b>Fecha Requisición Origen:</b></td>
                                                    <td class="bg-gray-light">{{(solicitud.complemento) ? solicitud.complemento.fecha_requisicion_origen_format : '------------'}}</td>
                                                    <td class="bg-gray-light"><b>Folio Requisición Origen:</b></td>
                                                    <td class="bg-gray-light">{{(solicitud.complemento) ? solicitud.complemento.requisicion_origen : '------------'}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Departamento Responsable:</b></td>
                                                    <td class="bg-gray-light">{{(solicitud.complemento) ? solicitud.complemento.area_compradora.descripcion : '------------'}}</td>
                                                    <td class="bg-gray-light"><b>Tipo:</b></td>
                                                    <td class="bg-gray-light">{{(solicitud.complemento) ? solicitud.complemento.tipo.descripcion : '------------'}}</td>
                                                    <td class="bg-gray-light"><b>Área Solicitante:</b></td>
                                                    <td class="bg-gray-light">{{(solicitud.complemento) ? solicitud.complemento.area_solicitante.descripcion : '------------'}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Concepto:</b></td>
                                                    <td class="bg-gray-light" colspan="5">{{(solicitud.complemento) ? solicitud.complemento.concepto : '------------'}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Usuario Registró:</b></td>
                                                    <td class="bg-gray-light" colspan="2">{{(solicitud.usuario) ? solicitud.usuario.nombre : '------------'}}</td>
                                                    <td class="bg-gray-light"><b>Fecha / Hora de Registro:</b></td>
                                                    <td class="bg-gray-light" colspan="2">{{solicitud.fecha_registro}}</td>
                                                </tr>
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
                                                        <th class="no_parte">No. de Parte</th>
                                                        <th>Descripción</th>
                                                        <th class="unidad">Unidad</th>
                                                        <th class="no_parte">Cantidad</th>
                                                        <th class="fecha">Fecha de Entrega</th>
                                                        <th>Destino</th>
                                                        <th>Observaciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(partida, i) in solicitud.partidas.data">
                                                        <td>{{i+1}}</td>
                                                        <td style="text-align: center"><b>{{partida.material.numero_parte}}</b></td>
                                                        <td style="text-align: center">{{partida.material.descripcion}}</td>
                                                        <td style="text-align: center">{{partida.material.unidad}}</td>
                                                        <td style="text-align: center">{{partida.cantidad}}</td>
                                                        <td style="text-align: center">{{(partida.entrega) ? partida.entrega.fecha_format : '------------'}}</td>

                                                        <td v-if="partida.entrega.destino_path" :title="`${partida.entrega.destino_path}`"><u>{{partida.entrega.destino_descripcion}}</u></td>
                                                        <td v-else >{{partida.entrega.destino_descripcion}}</td>

                                                        <td style="text-align: left">{{(partida.complemento) ? partida.complemento.observaciones : '------------'}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row col-md-12">
                                        <div class="col-md-2"><b>Observaciones:</b></div>
                                        <div class="col-md-10">{{solicitud.observaciones}}</div>
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
        name: "solicitud-show",
        props: ['id', 'cotizacion'],
        data(){
            return{
                cargando: false,
            }
        },
        methods: {
            find() {

                this.cargando = true;
                this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', null);
                return this.$store.dispatch('compras/solicitud-compra/find', {
                    id: this.id,
                    params:{include: [
                            'complemento',
                            'partidas.complemento', 'partidas.entrega']}
                }).then(data => {
                    this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', data);

                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                    this.cargando = false;

                })
            }
        },
        computed: {
            solicitud() {
                return this.$store.getters['compras/solicitud-compra/currentSolicitud']
            },
            boton()
            {
                return (this.cotizacion) ? this.cotizacion :false;
            }
        }
    }
</script>

<style scoped>

</style>
