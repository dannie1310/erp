<template>
    <span>
        <div >
            <div class="row">
                <div class="col-12">
                    <div class="card">
			            <div class="card-body">
                            <partial-show v-bind:id="id" v-bind:base="base" />
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
    import PartialShow from "./partials/PartialShow";
    export default {
        name: "estimacion-proveedor-show",
        components: {PartialShow},
        props: ['id', 'base'],
        data(){
            return{
                cargando: false,
            }
        },
        mounted() {

        },
        methods: {
            salir() {
                this.$router.push({name: 'estimacion-proveedor'});
            },
        }
    }
</script>
