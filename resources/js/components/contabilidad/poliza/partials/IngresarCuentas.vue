<template>
    <span>
        <button class="btn btn-app btn-info float-right" data-toggle="modal" data-target="#agregar-cuentas-modal" v-if="$root.can('ingresar_cuenta_faltante_movimiento_prepoliza') && movs.length">
            <i class="fa fa-dollar"></i> Ingresar cuentas faltantes
        </button>
        <div ref="modalCuentasFaltantes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="agregar-cuentas-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Cuentas Faltantes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validar()">
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Tipo Cuenta Contable</th>
                                        <th>Cuenta Contable</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(movimiento, index) in movs">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ movimiento.descripcion ? movimiento.descripcion : 'No Registrada' }}</td>
                                        <td class="form-group">
                                            <input
                                                    v-mask="{regex: datosContables}"
                                                    type="text"
                                                    class="form-control"
                                                    :name="`cuenta_contable[${index}]`"
                                                    v-model="movimiento.cuenta_contable"
                                                    v-validate="{required: true, regex: datosContables}"
                                                    data-vv-as="Cuenta Contable"
                                                    :class="{'is-invalid': errors.has(`cuenta_contable[${index}]`)}"
                                            >
                                            <div class="invalid-feedback" v-show="errors.has(`cuenta_contable[${index}]`)">{{ errors.first(`cuenta_contable[${index}]`) }}</div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
    export default {
        name: "poliza-ingresar-cuentas",
        props:['movimientos'],
        data() {
            return {
                movs: []
            }
        },
        mounted() {
            this.movs = JSON.parse(JSON.stringify(this.movimientos));
            $(this.$refs.modalCuentasFaltantes).on('show.bs.modal', (event) => {
                this.movs.forEach((mov) => {
                    this.$set(mov, 'cuenta_contable', '');
                });
                this.$validator.reset();
            })
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        }
    }
</script>