<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>

        <div ref="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detalles del Movimiento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table v-if="movimiento" class="table">
                                <tbody>
                                <tr>
                                    <th style="text-align: right">Número de Folio</th>
                                    <td>{{ movimiento.numero_folio}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Tipo de Movimiento</th>
                                    <td>{{ movimiento.tipo.descripcion }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Cuenta</th>
                                    <td>{{ movimiento.cuenta ? movimiento.cuenta.numero : '' }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Referencia</th>
                                    <td>{{ movimiento.transaccion ? movimiento.transaccion.referencia : '' }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Fecha</th>
                                    <td>{{ (new Date(movimiento.fecha)).toLocaleDateString('es') }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Cumplimiento</th>
                                    <td>{{ movimiento.transaccion ? (new Date(movimiento.transaccion.cumplimiento)).toLocaleDateString('es') : '' }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Vencimiento</th>
                                    <td>{{ movimiento.transaccion ? (new Date(movimiento.transaccion.vencimiento)).toLocaleDateString('es') : '' }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Importe</th>
                                    <td>$ {{ parseFloat(movimiento.importe).formatMoney(2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Impuesto</th>
                                    <td>$ {{ parseFloat(movimiento.impuesto).formatMoney(2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Total</th>
                                    <th>$ {{ (parseFloat(movimiento.importe) + parseFloat(movimiento.impuesto)).formatMoney(2, '.', ',') }}</th>
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
        name: "movimiento-bancario-show",
        props: ['id'],
        methods: {
            find(id) {
                this.$store.dispatch('tesoreria/movimiento-bancario/find', {
                    id: id,
                    params: {
                        include: 'cuenta.empresa,transaccion'
                    }
                }).then(() => {
                    $(this.$refs.modal).modal('show')
                })
            }
        },

        computed: {
            movimiento() {
                return this.$store.getters['tesoreria/movimiento-bancario/currentMovimiento']
            }
        }
    }
</script>