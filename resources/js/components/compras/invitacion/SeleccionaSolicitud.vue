<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <form role="form" @submit.prevent="validate">

                            <div class="modal-body">
                                <select-solicitud-compra ></select-solicitud-compra>
                            </div>

                             <div class="modal-footer">
                                 <button type="button" class="btn btn-secondary" v-on:click="salir">
                                        <i class="fa fa-angle-left"></i>
                                        Regresar</button>
                                    <button type="submit" :disabled="!solicitud" class="btn btn-primary">
                                        Continuar
                                        <i class="fa fa-angle-right"></i>
                                    </button>
                             </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    import DatosSolicitudCompra from "../solicitud-compra/partials/DatosSolicitudCompra";
    import SelectSolicitudCompra from "../solicitud-compra/partials/SelectSolicitudCompra";
    //import DatosContratoProyectado from "../proyectado/partials/DatosContratoProyectado";
    export default {
        name: "selecciona-solicitud-compra-invitacion",
        components: {
            SelectSolicitudCompra,
            DatosSolicitudCompra,
            /*DatosContratoProyectado,*/ ModelListSelect},
        data() {
            return {
                cargando: false,
                id_solicitud: '',
            }
        },
        mounted() {
            this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', null);
            this.$validator.reset();
        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[${item.numero_folio_format}]-[ ${item.concepto} ]-[ ${item.observaciones} ]`;
            },
            salir()
            {
                 this.$router.push({name: 'invitacion-compra'});
            },
            validate() {

                this.$validator.validate().then(result => {
                    if (result) {

                        this.$router.push({name: 'invitacion-compra-create', params: {id_solicitud: this.solicitud.id}});
                    }
                });
            },
        },
        computed: {
            solicitud(){
                return this.$store.getters['compras/solicitud-compra/currentSolicitud'];
            },
        },
    }
</script>

<style scoped>

</style>
