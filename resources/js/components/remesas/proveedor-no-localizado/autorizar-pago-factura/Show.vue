<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-eye"></i> CONSULTAR DETALLE DE AUTORIZACIÓN O RECHAZO DEL PAGO PARA PROVEEDOR NO LOCALIZADO</h5>
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
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">Proyecto: </dt>
                                        <dd class="col-sm-8">{{documento.documento.remesaSinScope.proyecto_descripcion}}</dd>
                                        <dt class="col-sm-4">Año:</dt>
                                        <dd class="col-sm-8">{{documento.documento.remesaSinScope.año}}</dd>
                                        <dt class="col-sm-4">Semana: </dt>
                                        <dd class="col-sm-8">{{documento.documento.remesaSinScope.semana}}</dd>
                                        <dt class="col-sm-4">Folio de Remesa: </dt>
                                        <dd class="col-sm-8">{{documento.documento.remesaSinScope.folio}}</dd>
                                        <dt class="col-sm-4">Tipo de Remesa: </dt>
                                        <dd class="col-sm-8">{{documento.documento.remesaSinScope.tipo}}</dd>
                                        <dt class="col-sm-4">Proveedor: </dt>
                                        <dd class="col-sm-8">{{documento.documento.proveedor}} ({{documento.documento.rfc}})</dd>
                                        <dt class="col-sm-4">Estado: </dt>
                                        <dd class="col-sm-8"><span class="badge" :style="{'background-color': documento.estado_color}">{{ documento.estado_format }}</span></dd>
                                        <dt class="col-sm-4" v-if="documento.estado == 1 ">Fecha de Autorización: </dt>
                                        <dd class="col-sm-8" v-if="documento.estado == 1">{{documento.fecha}}</dd>
                                        <dt class="col-sm-4" v-if="documento.estado == 2 ">Fecha de Rechazado: </dt>
                                        <dd class="col-sm-8" v-if="documento.estado == 2">{{documento.fecha_rechazo}}</dd>
                                        <dt class="col-sm-4">Usuario Registro: </dt>
                                        <dd class="col-sm-8">{{documento.registro}}</dd>
                                        <dt class="col-sm-4" v-if="documento.estado == 1 ">Usuario Autorizó: </dt>
                                        <dd class="col-sm-8" v-if="documento.estado == 1">{{documento.aprobo}}</dd>
                                        <dt class="col-sm-4" v-if="documento.estado == 2 ">Usuario Rechazó: </dt>
                                        <dd class="col-sm-8" v-if="documento.estado == 2">{{documento.rechazo}}</dd>
                                        <dt class="col-sm-4" v-if="documento.estado == 2 ">Motivo de Rechazó: </dt>
                                        <dd class="col-sm-8" v-if="documento.estado == 2">{{documento.motivo}}</dd>
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
        name: "autorizar-pago-factura-show",
        props: ['id'],
        data() {
            return {
                cargando : true
            }
        },
        methods: {
            salir() {
                this.$store.commit('remesas/documento-no-localizado/SET_DOCUMENTO', null);
                $(this.$refs.modal).modal('hide');
            },
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('remesas/documento-no-localizado/SET_DOCUMENTO', null);
                return this.$store.dispatch('remesas/documento-no-localizado/find', {
                    id: this.id,
                    params: {include : 'documento.remesaSinScope'}
                }).then(data => {
                    this.$store.commit('remesas/documento-no-localizado/SET_DOCUMENTO', data);
                }).finally(()=>
                {
                    this.cargando = false;
                })
            },
        },
        computed: {
            documento() {
                return this.$store.getters['remesas/documento-no-localizado/currentDocumento']
            }
        }
    }
</script>

<style scoped>

</style>
