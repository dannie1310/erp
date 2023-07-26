<template>
    <span>
        <div class="row" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <poliza-partial-show v-bind:id="this.id" v-bind:id_empresa="this.id_empresa"></poliza-partial-show>
                        <poliza-contpaq-lista-posibles-cfdi></poliza-contpaq-lista-posibles-cfdi>
                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                            <button type="button" class="btn btn-secondary " v-on:click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                            <button @click="asociar"  class="btn btn-danger">
                                <i class="fa fa-share-alt"></i> Asociar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import CFDI from "../../../fiscal/cfd/cfd-sat/CFDI";
import PolizaPartialShow from "../partials/PartialShow";
import PDFPoliza from "../partials/PDFPoliza";
import ListaCfdiAsociar from "../ListaCFDI.vue";
import PolizaContpaqListaPosiblesCfdi from "../../../contabilidad/poliza-contpaq/partials/ListaPosiblesCFDI.vue";

export default {
    name: "poliza-asociacion-asocia-cfdi",
    props : ['id', 'id_empresa'],
    components: {PolizaContpaqListaPosiblesCfdi, ListaCfdiAsociar, PDFPoliza, PolizaPartialShow, CFDI},
    data() {
        return {
            cargando :false,
            cfdi_store : [],

        }
    },
    mounted() {
        //this.find();
    },
    methods :{
        regresar() {
            this.$router.push({name: 'poliza-contpaq-asociacion', params: {id_empresa: this.id_empresa}});
        },
        asociar()
        {
            var item_a_guardar = 0;
            let _self = this;

            _self.cfdi_store = [];

            this.poliza.posibles_cfdi.data.forEach(function(element) {
                if(element.seleccionado === true || element.seleccionado === 1)
                {
                    item_a_guardar = item_a_guardar + 1;
                    _self.cfdi_store.push(element.id);
                }
            });
            if(item_a_guardar > 0)
            {
                return this.$store.dispatch('contabilidadGeneral/poliza/asociarCFDI',
                    {"cfdi":_self.cfdi_store,
                        "id_poliza":_self.id,
                        "id_empresa":_self.id_empresa}
                ).then((data) => {
                    this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', data);
                    this.$emit('success');
                    this.$router.push({name: 'poliza-contpaq-asociacion-show', params: {id: this.id, id_empresa: this.id_empresa}});
                }).finally(() => {
                });
            }
        },

        updateSeleccionado(cfdi) {
            this.$store.commit('contabilidadGeneral/poliza/SET_POSIBLE_CFDI', cfdi);

            let new_value = false;


            if(cfdi.seleccionado === true){
                new_value = false;
            } else {
                new_value = true;
            }
            this.$store.commit('contabilidadGeneral/poliza/UPDATE_ATTRIBUTE_POSIBLE_CFDI', {attribute: 'seleccionado', value: new_value});
        },
    },
    watch: {
        checkbox_toggle(value){
            if(value == 1){
                this.cfdis.forEach(function(element) {
                    element.seleccionado = 1;
                });
            } else {
                this.cfdis.forEach(function(element) {
                    element.seleccionado = 0;
                });
            }
        },
    },
    computed: {
        poliza(){
            return this.$store.getters['contabilidadGeneral/poliza/currentPoliza'];
        },
    },
}
</script>
<style scoped>

table.table-fs-sm{
    font-size: 10px;
}

</style>
