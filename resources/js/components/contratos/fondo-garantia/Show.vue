<template>
    <span>
        <button @click="find(id)" v-if="$root.can('consultar_cuenta_almacen')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>

        <div ref="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detalles del Fondo de Garantía</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "fondo-garantia-show",
        props: ['id'],
        methods: {
            find(id) {
                return this.$store.dispatch('contratos/fondo-garantia/find', {
                    id: id,
                    params: { include: 'subcontrato.empresa' }
                }).then(() => {
                    $(this.$refs.modal).modal('show')
                })
            },
        },
        computed: {
            fondo_garantia() {
                return this.$store.getters['contratos/fondo-garantia/currentFondoGarantia']
            }
        }
    }
</script>