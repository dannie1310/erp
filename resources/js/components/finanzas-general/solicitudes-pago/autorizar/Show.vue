<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-eye"></i> Solicitud de Pago</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="cargando">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="card">
                                <div class="card-body" >
                                    <dl class="row">
                                        <dt class="col-sm-4">Proyecto: </dt>
                                        <dd class="col-sm-8">{{solicitud.proyecto}}</dd>
                                        <dt class="col-sm-4">Fecha: </dt>
                                        <dd class="col-sm-8">{{solicitud.fecha}}</dd>
                                        <dt class="col-sm-4">Número de Folio: </dt>
                                        <dd class="col-sm-8">{{solicitud.numero_folio}}</dd>
                                        <dt class="col-sm-4">Tipo de Solicitud: </dt>
                                        <dd class="col-sm-8">{{solicitud.tipo_txt}}</dd>
                                        <dt class="col-sm-4">Proveedor: </dt>
                                        <dd class="col-sm-8">{{solicitud.razon_social}} ({{solicitud.rfc}})</dd>
                                        <dt class="col-sm-4" v-if="solicitud.estatus == 1 ">Fecha de Autorización: </dt>
                                        <dd class="col-sm-8" v-if="solicitud.estatus == 1">{{solicitud.fecha}}</dd>
                                        <dt class="col-sm-4" v-if="solicitud.estatus == 2 ">Fecha de Rechazado: </dt>
                                        <dd class="col-sm-8" v-if="solicitud.estatus == 2">{{solicitud.fecha_rechazo}}</dd>
                                        <dt class="col-sm-4">Usuario Solicita Autorización: </dt>
                                        <dd class="col-sm-8">{{solicitud.registro}} ({{solicitud.fecha_hora_registro}})</dd>
                                        <dt class="col-sm-4" v-if="solicitud.estatus == 1 ">Usuario Autorizó: </dt>
                                        <dd class="col-sm-8" v-if="solicitud.estatus == 1">{{solicitud.aprobo}}</dd>
                                        <dt class="col-sm-4" v-if="solicitud.estatus == 2 ">Usuario Rechazó: </dt>
                                        <dd class="col-sm-8" v-if="solicitud.estatus == 2">{{solicitud.rechazo}}</dd>
                                        <dt class="col-sm-4" v-if="solicitud.estatus == 2 ">Motivo de Rechazó: </dt>
                                        <dd class="col-sm-8" v-if="solicitud.estatus == 2">{{solicitud.motivo}}</dd>
                                        <dt class="col-sm-4">Observaciones: </dt>
                                        <dd class="col-sm-8">{{solicitud.observaciones}}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir"><i class="fa fa-close"></i> Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "autorizar-solicitud-pago-show",
        props: ['id'],
        data() {
            return {
                cargando : true
            }
        },
        methods: {
            salir() {
                this.$store.commit('finanzas-general/solicitud-pago/SET_SOLICITUD', null);
                $(this.$refs.modal).modal('hide');
            },
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('finanzas-general/solicitud-pago/SET_SOLICITUD', null);
                return this.$store.dispatch('finanzas-general/solicitud-pago/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('finanzas-general/solicitud-pago/SET_SOLICITUD', data);
                }).finally(()=>
                {
                    this.cargando = false;
                })
            },
        },
        computed: {
            solicitud() {
                return this.$store.getters['finanzas-general/solicitud-pago/currentSolicitud']
            }
        }
    }
</script>

<style scoped>

</style>
