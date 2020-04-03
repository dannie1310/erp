<template>
    <span>
        <button @click="find()" type="button"class="btn btn-sm btn-outline-secondary" :disabled="cargando" title="Ver Solicitud">
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
            <div class="modal-body">
                        
                            <div class="row">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                        <div class="col-12" v-if="solicitud">
                                            <h5>Folio: &nbsp; <b>{{solicitud.numero_folio_format}}</b></h5>
                                        </div>
                                    </div>
                                        <table class="table" v-if="solicitud">
                                                    <tbody>
                                                        <tr>
                                                            <td class="bg-gray-light"><b>Fecha Requisición Origen:</b></td>
                                                            <td class="bg-gray-light">{{(solicitud.complemento) ? solicitud.complemento.fecha_requisicion_origen_format : '------------'}}</td>
                                                            <td class="bg-gray-light"><b>Fecha:</b></td>
                                                            <td class="bg-gray-light"> {{solicitud.fecha_format}} </td>
                                                            <td class="bg-gray-light"><b>Folio Requisición Origen:</b></td>
                                                            <td class="bg-gray-light">{{(solicitud.complemento) ? solicitud.complemento.folio : '------------'}}</td>
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
                                                            <td class="bg-gray-light" colspan="3">{{(solicitud.complemento) ? solicitud.complemento.concepto : '------------'}}</td>
                                                            <td class="bg-gray-light"><b>Usuario Registró:</b></td>
                                                            <td class="bg-gray-light">{{(solicitud.usuario) ? solicitud.usuario.nombre : '------------'}}</td>
                                                        </tr>
                                                        <!-- <tr>
                                                            <td class="bg-gray-light"><b>Observaciones:</b></td>
                                                            <td class="bg-gray-light" colspan="5">------------------------</td>
                                                        </tr> -->
                                                    </tbody>
                                                </table>
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
                                                <tbody v-if="solicitud">
                                                    <tr v-for="(partida, i) in solicitud.partidas.data">
                                                        <td>{{i+1}}</td>
                                                        <td style="text-align: center"><b>{{partida.material.numero_parte}}</b></td>
                                                        <td style="text-align: center">{{partida.material.descripcion}}</td>
                                                        <td style="text-align: center">{{partida.cantidad}}</td>
                                                        <td style="text-align: center">{{(partida.entrega) ? partida.entrega.fecha_format : '------------'}}</td>
                                                        <td>{{(partida.concepto) ? partida.concepto.path : '------------'}}</td>
                                                        <td style="text-align: left">{{(partida.complemento) ? partida.complemento.observaciones : '------------'}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                  </div>
                                  <table class="table" v-if="solicitud">
                                                    <tbody>
                                                        <tr>
                                                            <td style="text-align:center;" class="bg-gray-light"><b>Observaciones:</b></td>
                                                            <td class="bg-gray-light">{{solicitud.observaciones}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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
        props: ['id'],
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
                    params:{include:
                     ['complemento.area_compradora',
                      'complemento.area_solicitante',
                      'complemento.tipo',
                      'partidas.material',
                      'partidas.complemento',
                      'partidas.concepto',
                      'partidas.entrega']}
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
            }
        }
    }
</script>

<style scoped>

</style>
