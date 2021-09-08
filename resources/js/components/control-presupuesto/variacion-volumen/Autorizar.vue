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
                <button type="button" @click="autorizar()" v-if="$root.can('autorizar_variacion_volumen') && solicitud && solicitud.id_estatus == 1" :disabled="cargando" class="btn btn-danger float-right" >
                    <i class="fa fa-thumbs-up"></i>
                    Autorizar
                </button>
                <RechazarVariacionVolumen @created="find()" v-if="solicitud && solicitud.id_estatus == 1" v-bind:id="id" ></RechazarVariacionVolumen>
                <PdfVariacion v-bind:id="id" v-bind:txt="'Formato'"></PdfVariacion>
                <button type="button" class="btn btn-secondary" v-on:click="regresar"><i class="fa fa-angle-left"></i>Regresar</button>
            </div>
        </div>

    </span>
</template>

<script>
import PdfVariacion from './partials/FormatoVariacionVolumen';
import RechazarVariacionVolumen from './partials/RechazarVariacionVolumen';
import SolicitudCambioPresupuestoPartialShow from "./partials/PartialShow";
export default {
    name: "variacion-volumen-show",
    components: {SolicitudCambioPresupuestoPartialShow, PdfVariacion, RechazarVariacionVolumen},
    props: ['id'],
    data() {
        return {
            cargando:false,
        }
    },
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
        autorizar(){
            return this.$store.dispatch('control-presupuesto/variacion-volumen/autorizar', {
                id: this.id,
                params: {
                    include: ['partidas'],
                }
            }).then(data => {
                this.$router.push({name: 'variacion-volumen'});
            });
        },
        regresar() {
            this.$router.push({name: 'variacion-volumen'});
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
