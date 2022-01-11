<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <form role="form" @submit.prevent="validate">

                            <div class="modal-body">
                                <select-contrato-proyectado></select-contrato-proyectado>
                            </div>

                             <div class="modal-footer">
                                 <button type="button" class="btn btn-secondary" v-on:click="salir">
                                        <i class="fa fa-angle-left"></i>
                                        Regresar</button>
                                    <button type="submit" :disabled="!contrato" class="btn btn-primary">
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
    import DatosContratoProyectado from "../proyectado/partials/DatosContratoProyectado";
    import SelectContratoProyectado from "../proyectado/partials/SelectContratoProyectado";
    export default {
        name: "selecciona-contrato-invitacion",
        components: {
            SelectContratoProyectado,
            DatosContratoProyectado, ModelListSelect},
        data() {
            return {
                cargando: false,
            }
        },
        mounted() {
            this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', null);
            this.$validator.reset();
        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[${item.numero_folio_format}]-[ ${item.concepto} ]-[ ${item.observaciones} ]`;
            },
            salir()
            {
                 this.$router.push({name: 'invitacion-contrato'});
            },
            validate() {

                this.$validator.validate().then(result => {
                    if (result) {

                        this.$router.push({name: 'invitacion-contrato-create', params: {id_contrato: this.contrato.id}});
                    }
                });
            },
        },
        computed: {
            contrato(){
                return this.$store.getters['contratos/contrato-proyectado/currentContrato'];
            },
        },
    }
</script>

<style scoped>

</style>
