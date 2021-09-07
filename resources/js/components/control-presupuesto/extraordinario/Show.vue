<template>
    <span>
        <div class="card" v-if="cargando">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" v-if="!cargando">
			<div class="card-body">
                <solicitud-cambio-presupuesto-partial-show v-bind:solicitud = solicitud></solicitud-cambio-presupuesto-partial-show>
			</div>
            <div class="modal-footer">
                <PdfVariacion v-bind:id="id" v-bind:txt="'Formato'"></PdfVariacion>
                <button type="button" class="btn btn-secondary " v-on:click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
            </div>
        </div>

    </span>
</template>

<script>
import PdfVariacion from './partials/Formato';
import SolicitudCambioPresupuestoPartialShow from "./partials/PartialShow";
export default {
    name: "variacion-volumen-show",
    components: {SolicitudCambioPresupuestoPartialShow, PdfVariacion},
    props: ['id'],
    data() {
        return {
            cargando:false,
        }
    },
    methods: {
        find() {
            this.cargando = true;
            this.$store.commit('control-presupuesto/extraordinario/SET_EXTRAORDINARIO', null);
            return this.$store.dispatch('control-presupuesto/extraordinario/find', {
                id: this.id,
                params: {
                    include: ['partidas'],
                }
            }).then(data => {
                this.$store.commit('control-presupuesto/extraordinario/SET_EXTRAORDINARIO', data);
            }) .finally(() => {
                this.cargando = false;
            })
        },
        autorizar(){
            return this.$store.dispatch('control-presupuesto/extraordinario/autorizar', {
                id: this.id,
                params: {
                    include: ['partidas'],
                }
            }).then(data => {
                this.find();
            }) .finally(() => {
                this.cargando = false;
            })
        },
        regresar() {
            this.$router.push({name: 'extraordinario'});
        },
    },
    computed: {
        solicitud() {
            return this.$store.getters['control-presupuesto/extraordinario/currentExtraordinario']
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
