<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4> <i class="fa fa-list-alt"></i> DETALLE DE LA SOLICITUD </h4>
                        </div>
                    </div>
                    <div class="modal-body" v-if="solicitud">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><b>Tipo de Solicitud de Cambio: </b></label>
                                    {{ solicitud.tipo_orden }}
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group">
                                    <label><b>Número de Folio: </b></label>
                                    # {{ solicitud.numero_folio }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><b>Usuario que Solicita: </b></label>
                                    {{ solicitud.usuario.nombre }}
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><b>Fecha de solicitud: </b></label>
                                    {{ solicitud.fecha_solicitud }}
                                </div>
                            </div>
                            
                           
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><b>Area Solicitante: </b></label>
                                    {{ solicitud.area_solicitante }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><b>Motivo: </b></label>
                                    {{ solicitud.motivo }}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><b>Estatus: </b></label>
                                    {{ solicitud.estatus }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "action-buttons",
    components: {},
    props: ['id'],
    methods: {
        find() {
            this.cargando = true;
            this.$store.commit('control-presupuesto/variacion-volumen/SET_VARIACION', null);
            return this.$store.dispatch('control-presupuesto/variacion-volumen/find', {
                id: this.id,
                params: {
                    include: ['partidas'],
                }
            }).then(data => {
                this.$store.commit('control-presupuesto/variacion-volumen/SET_VARIACION', data);
            }) .finally(() => {
                this.cargando = false;
            })
        },
    },
    computed: {
        solicitud() {
            return this.$store.getters['control-presupuesto/variacion-volumen/currentVariacion']
        },
    },
    mounted() {
        this.$Progress.start();
        this.find()
        .finally(() => {
            this.$Progress.finish();
        })
    }
}
</script>

<style>

</style>