<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Solicitar Movimiento">
            <i class="fa fa-edit"></i>
        </button>

        <div ref="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Solicitar Movimiento a Fondo de Garantía</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table v-if="movimiento" class="table">
                                <tbody>
                                <tr>
                                    <th style="text-align: right">Subcontrato:</th>
                                    <td>{{ fondoGarantia.subcontrato.numero_folio}}</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "fondo-garantia-solicitar-movimiento",
        props: ['id'],
        methods: {

        },

        computed: {

        }
    }
</script>