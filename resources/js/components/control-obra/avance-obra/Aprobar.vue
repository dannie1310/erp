<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <tabla-datos v-bind:id="id" @cargaFinalizada="iniciar"/>
                    </div>
                    <div class="card-footer">
                        <div class="pull-right">
                            <button type="button" class="btn btn-secondary" v-on:click="salir">
                                <i class="fa fa-angle-left"></i>Regresar
                            </button>
                            <button type="submit" class="btn btn-primary" @click="aprobar" :disabled="fin_carga == 0">
                                <i class="fa fa-thumbs-o-up"></i>Aprobar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import TablaDatos from "./partials/TablaDatos";
    export default {
        name: "aprobar-avance-obra",
        props: ['id'],
        components: { TablaDatos },
        data(){
            return{
                fin_carga: 0
            }
        },
        methods: {
            iniciar() {
                this.fin_carga = 1;
            },
            salir() {
                this.$router.push({name: 'avance-obra'});
            },
            aprobar() {
                return this.$store.dispatch('controlObra/avance-obra/aprobar', {
                    id: this.id
                })
                .then(() => {
                    this.salir();
                })
            },
        }
    }
</script>

<style scoped>

</style>
