<template>
    <span>
        <div class="row" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <layout-partial-show v-bind:id ="this.id"></layout-partial-show>
                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                            <button type="button" class="btn btn-secondary " v-on:click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import PDFPoliza from "../poliza/partials/PDFPoliza.vue";
import PolizaPartialShow from "../poliza/partials/PartialShow.vue";
import LayoutPartialShow from "./partials/PartialShow.vue";

export default {
    name: "layout-pasivo-show",
    props : ['id'],
    components: {LayoutPartialShow},
    mounted() {
        this.find();
    },
    methods: {
        regresar() {
            this.$router.push({name: 'layouts-pasivos'});
        },
        find()
        {
            if(this.layout == null){
                this.cargando = true;
                this.$store.commit('contabilidadGeneral/layout-pasivo/SET_LAYOUT', null);
                return this.$store.dispatch('contabilidadGeneral/layout-pasivo/find', {
                    id: this.id,
                    params: {
                        include: ['partidas'],
                    }
                }).then(data => {
                    this.$store.commit('contabilidadGeneral/layout-pasivo/SET_LAYOUT', data);
                    this.$store.commit('contabilidadGeneral/layout-pasivo-partida/SET_PASIVOS', data.partidas.data);

                }).finally(() => {
                    this.cargando = false;
                });
            }
            else if(this.id > 0 && this.id != this.layout.id) {
                this.cargando = true;
                this.$store.commit('contabilidadGeneral/layout-pasivo/SET_LAYOUT', null);
                return this.$store.dispatch('contabilidadGeneral/layout-pasivo/find', {
                    id: this.id,
                    params: {
                        include: ['partidas'],
                        id: this.id
                    }
                }).then(data => {
                    this.$store.commit('contabilidadGeneral/layout-pasivo/SET_LAYOUT', data);
                    this.$store.commit('contabilidadGeneral/layout-pasivo-partida/SET_PASIVOS', data.partidas.data);

                }).finally(() => {
                    this.cargando = false;
                });
            }
            else if(this.layout_parametro != null){
                this.$store.commit('contabilidadGeneral/layout-pasivo/SET_LAYOUT', this.layout_parametro);
                this.$store.commit('contabilidadGeneral/layout-pasivo-partida/SET_PASIVOS', this.layout_parametro.partidas.data);

                this.cargando = false;
            }
        }
    },
    computed: {
        layout(){
            return this.$store.getters['contabilidadGeneral/layout-pasivo/currentLayout'];
        },
        pasivos(){
            return this.$store.getters['contabilidadGeneral/layout-pasivo-partida/pasivos'];
        },
        actualizando() {
            return this.$store.getters['contabilidadGeneral/layout-pasivo/actualizando'];
        },
    },
}
</script>
<style scoped></style>
