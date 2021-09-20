<template>
    <span>
        <div >
            <div class="row">
                <div class="col-12">
                    <div class="card">
			            <div class="card-body">
                            <presupuesto-proveedor-partial-show v-bind:id="id" />
                        </div>
                        <div class="card-footer">
                            <div class="pull-right">
                                <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import PresupuestoProveedorPartialShow from './partials/PartialShow'
    export default {
        name: "presupuesto-show",
        components: {PresupuestoProveedorPartialShow},
        props: ['id'],
        data(){
            return{
                cargando: false,
            }
        },
        mounted() {

        },
        methods: {
            salir() {
                    this.$router.push({name: 'cotizacion-proveedor'});
            },
        },
    }
</script>

<style scoped>

</style>
