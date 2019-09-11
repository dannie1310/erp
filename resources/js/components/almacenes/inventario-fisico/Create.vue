<template>
    <span>
        <button @click="init" v-if="$root.can('solicitar_baja_cuenta_bancaria_empresa')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar Inventario
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> REGISTRAR INVENTARIO FÍSICO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                     <form role="form" @submit.prevent="store">
                        <div class="modal-body">
                            ¿Esta seguro que quiere iniciar el proceso de inventario fisico en la obra .....?
                        </div>
                         <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary":disabled="errors.count() > 0 || id_empresa =='' || id_cuenta ==''">Registrar</button>
                        </div>
                     </form>
                </div>
            </div>
          </div>
    </span>
</template>

<script>
    export default {
        name: "inventario-fisico-create",
        data() {
            return {
                cargando: false,
                folio: 4
            }
        },
        mounted(){
        },
        methods:{
            init() {
                this.cargando = true;
                $(this.$refs.modal).modal('show');
            },
            store() {
                return this.$store.dispatch('almacenes/inventario-fisico/store', this.$data)
                    .then(data => {
                        this.$emit('created', data);
                        $(this.$refs.modal).modal('hide');
                    }).finally( ()=>{
                        this.cargando = false;
                    });
            },
        }
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>