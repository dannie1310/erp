<template>
    <span>
        <div class="row" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <poliza-partial-show v-bind:id="this.id" v-bind:id_empresa="this.id_empresa"></poliza-partial-show>
                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                            <button type="button" class="btn btn-secondary " v-on:click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                            <PDFPoliza v-bind:id="this.id" v-bind:id_empresa="this.id_empresa" v-bind:txt_btn="'Ver PDF'"></PDFPoliza>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import PDFPoliza from "../../contabilidad-general/poliza/partials/PDFPoliza.vue";
import PolizaPartialShow from "../../contabilidad-general/poliza/partials/PartialShow.vue";

export default {
    name: "poliza-cfdi-show",
    props : ['id', 'id_empresa'],
    components: {PDFPoliza, PolizaPartialShow},
    methods :{
        regresar() {
            this.$router.push({name: 'poliza-cfdi-proyecto'});
        },
    },
    watch:{
        tipo_modal : {
            handler(tipo_modal) {
                if(tipo_modal !== '' && tipo_modal === 1){
                    this.init();
                }
            }
        },
    }
}
</script>
<style scoped></style>
