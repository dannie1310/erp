<template>
    <span>
        <div class="btn-group">
            <button title="Aprobar" @click="resumen('aprobar')" v-if="value.aprobar" type="button"
                    class="btn btn-sm btn-outline-success" :disabled="aprobando">
                <i v-if="aprobando" class="fa fa-spin fa-spinner"></i>
                <i v-else class="fa fa-thumbs-o-up"></i>
            </button>
            <button title="Revertir Aprobación" @click="resumen('revertir')" v-if="value.desaprobar" type="button"
                    class="btn btn-sm btn-outline-danger" :disabled="revirtiendo">
                <i v-if="revirtiendo" class="fa fa-spin fa-spinner"></i>
                <i v-else class="fa fa-thumbs-down"></i>
            </button>
            <button @click="show"  type="button" class="btn btn-sm btn-outline-secondary" title="Ver Estimación ">
                <i class="fa fa-eye"></i>
            </button>
            <button @click="eliminar" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar" v-if="value.delete && (value.estado == 0)" >
                <i class="fa fa-trash"></i>
            </button>
            <PDF v-bind:id="value.id" @click="value.id" ></PDF>
        </div>

        <!-- Modal -->
        <div class="modal fade" ref="resumen" tabindex="-1" role="dialog" aria-labelledby="resumenLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resumenLabel">Resumen de Estimación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table style="width: 100%" class="table table-stripped small">
                                <tbody>
                                <tr>
                                    <th style="text-align: left" colspan="2">Suma de Importes</th>
                                    <td style="text-align: right">{{ `$ ${(parseFloat(value.estimacion.monto) + parseFloat(value.estimacion.impuesto)).formatMoney(2)}` }}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left" colspan="2">Deductivas</th>
                                    <td style="text-align: right">$ 0</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left">Amortización de Anticipo</th>
                                    <td>0%</td>
                                    <th style="text-align: right">$ 0</th>
                                </tr>
                                <tr>
                                    <th style="text-align: left">Fondo de Garantia</th>
                                    <td>0%</td>
                                    <th style="text-align: right">$ 0</th>
                                </tr>
                                 <tr>
                                    <th style="text-align: left" colspan="2">Penalizaciones sin IVA</th>
                                    <td style="text-align: right">$ 0</td>
                                </tr>
                                 <tr>
                                    <th style="text-align: left" colspan="2">Subtotal</th>
                                    <td style="text-align: right">{{ `$ ${parseFloat(value.estimacion.monto).formatMoney(2)}` }}</td>
                                </tr>
                                 <tr>
                                    <th style="text-align: left" colspan="2">I.V.A.</th>
                                    <td style="text-align: right">{{ `$ ${parseFloat(value.estimacion.impuesto).formatMoney(2)}` }}</td>
                                </tr>
                                 <tr>
                                    <th style="text-align: left" colspan="2">Total</th>
                                    <th style="text-align: right">{{ `$ ${(parseFloat(value.estimacion.monto) + parseFloat(value.estimacion.impuesto)).formatMoney(2)}` }}</th>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button @click="aprobando ? aprobar() : desaprobar()" type="button" class="btn btn-primary" :disabled="guardando">
                            <span v-if="guardando">
                                <i class="fa fa-spin fa-spinner"></i>
                            </span>
                            <span v-else>
                                {{ aprobando ? 'Aprobar' : 'Revertir Aprobación' }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import PDF from '../FormatoEstimacion';
    import EstimacionShow from '../Show';

    export default {
        name: "action-buttons",
        components: {EstimacionShow, PDF},
        props: ['value'],
        data() {
            return {
                aprobando: false,
                revirtiendo: false,
                guardando: false
            }
        },

        mounted() {
            $(this.$refs.resumen).on('hidden.bs.modal', () => {
                this.aprobando = false;
                this.revirtiendo = false;
            })
        },

        methods: {
            resumen(opcion) {
                if (opcion == 'aprobar') {this.aprobando = true;}
                else {this.revirtiendo = true;}
                $(this.$refs.resumen).modal('show');
            },

            aprobar() {
                this.guardando = true;
                return this.$store.dispatch('contratos/estimacion/aprobar' ,{ id: this.value.id })
                    .then(() => {
                        this.$store.commit('contratos/estimacion/APROBAR_ESTIMACION', this.value.id);
                    })
                    .finally(() => {
                        this.guardando = false;
                        $(this.$refs.resumen).modal('hide');
                    })
            },

            desaprobar() {
                this.guardando = true;
                return this.$store.dispatch('contratos/estimacion/revertirAprobacion', {id: this.value.id})
                    .then(() => {
                        this.$store.commit('contratos/estimacion/REVERTIR_APROBACION', this.value.id);
                    })
                    .finally(() => {
                        this.guardando = false;
                        $(this.$refs.resumen).modal('hide');
                    })
            },
            show(){
                this.$router.push({ name:'estimacion-show', params: {id: this.value.id} });
            },
            eliminar() {
                console.log('eliminar')
                this.$router.push({name: 'estimacion-delete', params: {id: this.value.id}});
            }
        }
    }
</script>
