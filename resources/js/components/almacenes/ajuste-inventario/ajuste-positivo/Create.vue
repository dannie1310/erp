<template>
    <span>
         <button @click="init" v-if="$root.can('consultar_entrada_almacen')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Ajuste
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR AJUSTE POSITIVO (+) DE INVENTARIO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                     <form role="form" @submit.prevent="validate">
                        <div class="modal-body">

                        </div>
                     </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "ajuste-positivo-create",
        data() {
            return {
                cargando: false,
            }
        },
        methods: {
            init() {
                this.cargando = true;
                $(this.$refs.modal).modal('show');
            }
        }
    }
</script>

<style scoped>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>