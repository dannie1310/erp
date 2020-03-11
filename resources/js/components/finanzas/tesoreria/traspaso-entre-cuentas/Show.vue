<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>

        <div ref="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detalles del Traspaso</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table v-if="traspaso" class="table">
                                <tbody>
                                <tr>
                                    <th style="text-align: right">Número de Folio</th>
                                    <td>{{ traspaso.numero_folio}}</td>
                                </tr>

                                <tr>
                                    <th style="text-align: right">Cuenta Origen</th>
                                    <td>{{ traspaso.cuentaOrigen ? traspaso.cuentaOrigen.numero + ' ' + (traspaso.cuentaOrigen.abreviatura ? traspaso.cuentaOrigen.abreviatura : '') + ' (' + traspaso.cuentaOrigen.empresa.razon_social + ')' : '' }}</td>
                                </tr>

                                 <tr>
                                    <th style="text-align: right">Cuenta Destino</th>
                                    <td>{{ traspaso.cuentaDestino ? traspaso.cuentaDestino.numero + ' ' + (traspaso.cuentaDestino.abreviatura ? traspaso.cuentaDestino.abreviatura : '') + ' (' + traspaso.cuentaDestino.empresa.razon_social + ')' : '' }}</td>
                                </tr>
                                 <tr>
                                    <th style="text-align: right">Fecha</th>
                                    <td>{{ traspaso.fecha }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Cumplimiento</th>
                                    <td>{{ traspaso.traspasoTransaccion.debito ? traspaso.traspasoTransaccion.debito.cumplimiento : traspaso.traspasoTransaccion.credito.cumplimiento }}</td>
                                </tr>
                                  <tr>
                                    <th style="text-align: right">Importe</th>
                                    <td>$ {{ parseFloat(traspaso.importe).formatMoney(2, '.', ',') }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: right">Referencia</th>
                                    <td>{{ traspaso.traspasoTransaccion.debito ? traspaso.traspasoTransaccion.debito.referencia : traspaso.traspasoTransaccion.credito.cumplimiento }}</td>
                                </tr>

                                </tbody>
                            </table>
                            <div class="form-group" v-if="traspaso">
                                <label>Observaciones</label>
                                <p>{{ traspaso.observaciones }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "traspaso-entre-cuentas-show",
        props: ['id'],
        methods: {
            find(id) {
                this.$store.commit('finanzas/traspaso-entre-cuentas/SET_TRASPASO', null);
                return this.$store.dispatch('finanzas/traspaso-entre-cuentas/find', {
                    id: id,
                    params: { include: 'cuentaOrigen.empresa,cuentaDestino.empresa' }
                }).then(data => {
                    this.$store.commit('finanzas/traspaso-entre-cuentas/SET_TRASPASO', data);
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                })
            }
        },

        computed: {
            traspaso() {
                return this.$store.getters['finanzas/traspaso-entre-cuentas/currentTraspaso']
            }
        }
    }
</script>
