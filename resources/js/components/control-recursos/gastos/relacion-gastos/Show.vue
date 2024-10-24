<template>
    <span>
        <div class="card" v-if="!relacion">
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
                        <encabezado v-bind:relacion="relacion" />
                        <tabla-datos v-bind:relacion="relacion" />
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
    name: "relacion-show",
    components: { Encabezado, TablaDatos },
    props: ['id'],
    data(){
        return{
            cargando: false,
            relacion : null
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/relacion-gasto/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.relacion = data
            })
                .finally(()=> {
                    this.cargando = false;
                })
        },
        salir() {
            this.$router.push({name: 'relacion-gasto'});
        },
    },
}
</script>

<style scoped>

</style>
