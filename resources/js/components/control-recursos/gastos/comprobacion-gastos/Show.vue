<template>
    <span>
        <div class="card" v-if="!solicitud">
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

        <div class="card" v-else>
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <encabezado v-bind:solicitud="solicitud" />
                        <tabla-datos v-bind:solicitud="solicitud" />
                        <tabla-documentos v-bind:documentos="solicitud.comprobacion.documentos" v-if="solicitud.comprobacion" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary" v-on:click="salir">
                        <i class="fa fa-angle-left"></i>
                        Regresar
                    </button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import Encabezado from './partials/Encabezado';
import TablaDatos from "./partials/TablaDatos";
import TablaDocumentos from "./partials/TablaDocumentos";
export default {
    name: "comprobacion-show",
    components: { Encabezado, TablaDatos, TablaDocumentos },
    props: ['id'],
    data(){
        return{
            cargando: false,
            solicitud : null
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/solicitud-comprobacion/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.solicitud = data
            })
                .finally(()=> {
                    this.cargando = false;
                })
        },
        salir() {
            this.$router.push({name: 'comprobacion-gasto'});
        },
    },
}
</script>

<style scoped>

</style>
