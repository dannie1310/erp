<template>
    <span>
        <div class="card" v-if="!factura">
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
                        <encabezado v-bind:factura="factura" />
                        <tabla-datos v-bind:factura="factura" />
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
        name: "documento-show",
        components: { Encabezado, TablaDatos },
        props: ['id'],
        data(){
            return{
                cargando: false,
                factura : null
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {
                this.cargando = true;
                return this.$store.dispatch('controlRecursos/documento/find', {
                    id: this.id,
                    params: { scope: 'seriePorUsuario' }
                }).then(data => {
                    this.factura = data
                })
                    .finally(()=> {
                        this.cargando = false;
                    })
            },
            salir() {
                this.$router.go(-1);
            },
        },
    }
</script>

<style scoped>

</style>
