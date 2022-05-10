<template>
    <span>
        <div class="card" v-if="!avance">
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
                        <encabezado v-bind:avance="avance" />
                        <tabla-datos v-bind:avance="avance" />
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
    export default {
        name: "avance-subcontrato-show",
        components: { Encabezado, TablaDatos },
        props: ['id'],
        data(){
            return{
                cargando: false,
                avance : null
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {
                this.cargando = true;
                return this.$store.dispatch('contratos/avance-subcontrato/obtenerAvance', {
                    id: this.id,
                    params:{include: []}
                }).then(data => {
                    this.avance = data
                })
                    .finally(()=> {
                        this.cargando = false;
                    })
            },
            salir() {
                this.$router.push({name: 'avance-subcontrato'});
            },
        },
    }
</script>

<style scoped>

</style>
