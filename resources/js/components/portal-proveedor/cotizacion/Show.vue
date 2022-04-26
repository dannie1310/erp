<template>
    <span>
        <div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
			            <div class="card-body">
                            <cotizacion-proveedor-partial-show v-bind:id_invitacion="id" />
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
    import CotizacionProveedorPartialShow from "./partials/PartialShow";
    export default {
        name: "cotizacion-proveedor-show",
        components: {CotizacionProveedorPartialShow},
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
        }
    }
</script>
