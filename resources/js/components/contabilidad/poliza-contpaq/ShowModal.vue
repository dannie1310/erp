<template>
    <span>
        <span v-if="txt!='' && (txt_btn =='' || txt_btn == undefined)" @click="init" style="cursor: pointer; text-decoration: underline; color: #003eff" >{{txt}}</span>
        <button @click="init" type="button" class="btn btn-outline-success" :class="{'btn-sm': txt_btn==''}" title="Ver Póliza" v-else>
            <i class="fa fa-eye"></i>{{txt_btn}}
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> Póliza</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="mostrar">
                        <poliza-partial-show v-bind:id="this.id" v-bind:id_empresa="this.id_empresa"></poliza-partial-show>
                    </div>
                    <div class="modal-footer" v-if="mostrar">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times" ></i>
                            Cerrar
                        </button>
                        <PDFPoliza v-bind:id="this.id" v-bind:id_empresa="this.id_empresa" v-bind:txt_btn="'Ver PDF'"></PDFPoliza>
                    </div>
                </div>
            </div>
         </div>
    </span>
</template>

<script>
import PolizaShow from "../../contabilidad/poliza/Show";
import PolizaPartialShow from "./partials/PartialShow";
import PDFPoliza from "./partials/PDFPoliza";
export default {
    name: "poliza-show-modal",
    components: {PDFPoliza, PolizaPartialShow, PolizaShow},
    props: ['id','id_empresa', 'txt', 'txt_btn'],
    data() {
        return {
            mostrar : false,
        }
    },
    methods:{
        init() {
            this.mostrar = true;
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        }
    }
}
</script>

<style scoped>

</style>
