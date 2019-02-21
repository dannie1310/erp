<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_movimiento_bancario')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar Solicitud
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR SOLICITUD DE MOVIMIENTO A FONDO DE GARANTÍA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Tipo de Movimiento -->
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label>Subcontrato</label>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </span>
</template>

<script>
    export default {
        name: "solicitud-movimiento-fondo-garantia-create",
        data() {
            return {
                id_tipo_movimiento: '',
                id_cuenta: '',
                importe: '',
                impuesto: '',
                referencia: '',
                observaciones: '',
                cumplimiento: '',
                fecha: ''
            }
        },

        mounted() {
            this.getTiposMovimiento()
            this.getCuentas()
        },

        methods: {
            init() {
                $(this.$refs.modal).modal('show');

                    this.id_tipo_movimiento = '';
                    this.id_cuenta = '';
                    this.importe = '';
                    this.impuesto = '';
                    this.referencia = '';
                    this.observaciones = '';
                    this.cumplimiento = '';
                    this.fecha = '';

                this.$validator.reset()
            },

            getTiposMovimiento() {
                return this.$store.dispatch('tesoreria/tipo-movimiento/fetch');
            },

            getCuentas() {
                return this.$store.dispatch('cadeco/cuenta/fetch', {
                    include: 'empresa',
                    scope: 'paraTraspaso'
                })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },

            store() {
                return this.$store.dispatch('tesoreria/movimiento-bancario/store', this.$data)
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                    })
            }
        },

        computed: {
            tiposMovimiento() {
                return this.$store.getters['tesoreria/tipo-movimiento/tipos']
            },

            cuentas() {
                return this.$store.getters['cadeco/cuenta/cuentas']
            },
            total() {
                let impuesto = this.impuesto ? parseFloat(this.impuesto) : 0;
                let importe = this.importe ? parseFloat(this.importe) : 0;
                return importe + impuesto;
            }
        }
    }
</script>

<style scoped>

</style>