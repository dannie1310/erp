<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-success" title="Registrar Pago">
            <i class="fa fa-check"></i>
        </button>
         <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> Datos de Pago del Documento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                    </div>
                </div>
            </div>
         </div>
    </span>
</template>

<script>
    export default {
        name: "datos_pago",
        props: ['id'],
        methods: {
            find(){
                this.$store.commit('finanzas/factura/SET_FACTURA', null);
                return this.$store.dispatch('finanzas/factura/find', {
                    id: this.id,
                    params: {
                        scope: ['pendientePago', 'conDocumento'],
                        include: ['documento']
                    }
                }).then(data => {
                    this.$store.commit('finanzas/factura/SET_FACTURA', data);
                    $(this.$refs.modal).modal('show');
                })
            }
        },
        computed: {
            documento() {
                return this.$store.getters['finanzas/factura/currentFactura'];
            }
        }
    }
</script>

<style scoped>

</style>
