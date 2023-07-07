<template>
    <span>
        <div class="row" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <layout-partial-show-datos-carga></layout-partial-show-datos-carga>
                        <layout-partial-show-lista-pasivos-validar></layout-partial-show-lista-pasivos-validar>
                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                             <button type="button" class="btn btn-primary" v-on:click="asociarCFDI" :disabled="actualizando || cargando"><i class="fa fa-file-invoice-dollar" ></i>Asociar CFDI</button>
                             &nbsp;&nbsp;<button type="button" class="btn btn-secondary " v-on:click="regresar" :disabled="actualizando || cargando"><i class="fa fa-angle-left" ></i>Regresar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>

import LayoutPartialShow from "./partials/PartialShow.vue";
import LayoutPartialShowDatosCarga from "./partials/PartialShowDatosCarga.vue";
import LayoutPartialShowListaPasivosValidar from "./partials/PartialShowListaPasivosValidar.vue";

export default {
    name: "layout-pasivo-show",
    props : ['id'],
    components: {LayoutPartialShowListaPasivosValidar, LayoutPartialShowDatosCarga, LayoutPartialShow},
    mounted() {
        this.find();
    },
    data(){
        return {
            cargando:false,
        }
    },
    methods :{
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
                }).finally(() => {
                    this.cargando = false;
                });
            }
        },
        asociarCFDI() {
            this.$store.commit('contabilidadGeneral/layout-pasivo/SET_ACTUALIZANDO', true);

            let _self = this;

            return this.$store.dispatch('contabilidadGeneral/layout-pasivo/asociarCFDI',
                {
                    id: _self.id,
                    data: {},
                    config: {
                        params: { _method: 'POST'}
                    }
                })
                .then(data => {
                    this.$store.commit('contabilidadGeneral/layout-pasivo/SET_LAYOUT', data);
                }).finally(() => {
                    this.$store.commit('contabilidadGeneral/layout-pasivo/SET_ACTUALIZANDO', false);
                });

        },
    },
    computed: {
        layout(){
            return this.$store.getters['contabilidadGeneral/layout-pasivo/currentLayout'];
        },
        actualizando() {
            return this.$store.getters['contabilidadGeneral/layout-pasivo/actualizando'];
        },
    },
}
</script>
<style scoped></style>
