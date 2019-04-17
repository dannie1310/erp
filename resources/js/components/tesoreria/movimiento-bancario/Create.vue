<template>
    <span>
        <button @click="init" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar Movimiento
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR MOVIMIENTO BANCARIO</h5>
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
                                        <label for="id_tipo_movimiento">Tipo</label>
                                        <select
                                                class="form-control"
                                                name="id_tipo_movimiento"
                                                data-vv-as="Tipo"
                                                id="id_tipo_movimiento"
                                                v-model="id_tipo_movimiento"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('id_tipo_movimiento')}"
                                        >
                                            <option value>-- Tipo de Movimiento --</option>
                                            <option v-for="(item, index) in tiposMovimiento" :value="item.id">
                                                {{ item.descripcion }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_tipo_movimiento')">{{ errors.first('id_tipo_movimiento') }}</div>
                                    </div>
                                </div>

                                <!-- Cuenta -->
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_cuenta">Cuenta</label>
                                        <select
                                                class="form-control"
                                                name="id_cuenta"
                                                data-vv-as="Cuenta"
                                                id="id_cuenta"
                                                v-model="id_cuenta"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('id_cuenta')}"
                                        >
                                            <option value>-- Cuenta --</option>
                                            <option v-for="(item, index) in cuentas" :value="item.id">
                                                {{ `${item.numero} ${item.abreviatura } (${item.empresa.razon_social})` }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_cuenta')">{{ errors.first('id_cuenta') }}</div>
                                    </div>
                                </div>

                                <!-- Importe -->
                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="importe">Importe</label>
                                        <input
                                                type="number"
                                                step="any"
                                                class="form-control"
                                                id="importe"
                                                name="importe"
                                                v-model="importe"
                                                v-validate="{required: true, decimal: true}"
                                                data-vv-as="Importe"
                                                :class="{'is-invalid': errors.has('importe')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('importe')">{{ errors.first('importe') }}</div>
                                    </div>
                                </div>

                                <!-- Impuesto -->
                                <div class="col-md-4" v-if="id_tipo_movimiento == 4">
                                    <div class="form-group error-content">
                                        <label for="impuesto">Impuesto</label>
                                        <input
                                                type="number"
                                                step="any"
                                                name="impuesto"
                                                id="impuesto"
                                                class="form-control"
                                                v-model="impuesto"
                                                v-validate="{decimal: true}"
                                                data-vv-as="Impuesto"
                                                :class="{'is-invalid': errors.has('impuesto')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('impuesto')">{{ errors.first('impuesto') }}</div>
                                    </div>
                                </div>

                                <!-- Total -->
                                <div class="col-md-4" v-if="id_tipo_movimiento == 4">
                                    <div class="form-group">
                                        <label for="total">Total</label>
                                        <input
                                                type="number"
                                                step="any"
                                                readonly
                                                name="total"
                                                id="total"
                                                class="form-control"
                                                :value="total"
                                        >
                                    </div>
                                </div>

                                <!-- Referencia -->
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="referencia">Referencia</label>
                                        <input
                                                type="text"
                                                name="referencia"
                                                id="referencia"
                                                class="form-control"
                                                v-model="referencia"
                                                v-validate="{required: true}"
                                                data-vv-as="Referecia"
                                                :class="{'is-invalid': errors.has('referencia')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                    </div>
                                </div>

                                <!-- Fecha -->
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="fecha">Fecha</label>
                                        <input
                                                type="date"
                                                name="fecha"
                                                id="fecha"
                                                class="form-control"
                                                v-model="fecha"
                                                v-validate="{required: true, date_format: 'YYYY-MM-DD'}"
                                                data-vv-as="Fecha"
                                                :class="{'is-invalid': errors.has('fecha')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                    </div>
                                </div>

                                <!-- Cumplimiento -->
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="cumplimiento">Cumplimiento</label>
                                        <input
                                                type="date"
                                                name="cumplimiento"
                                                id="cumplimiento"
                                                class="form-control"
                                                v-model="cumplimiento"
                                                v-validate="{required: true, date_format: 'YYYY-MM-DD'}"
                                                data-vv-as="Cumplimiento"
                                                :class="{'is-invalid': errors.has('cumplimiento')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('cumplimiento')">{{ errors.first('cumplimiento') }}</div>
                                    </div>
                                </div>

                                <!-- Observaciones -->
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="observaciones">Observaciones</label>
                                        <textarea
                                                name="observaciones"
                                                id="observaciones"
                                                class="form-control"
                                                v-model="observaciones"
                                                v-validate="{required: true}"
                                                data-vv-as="Observaciones"
                                                :class="{'is-invalid': errors.has('observaciones')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </span>
</template>

<script>
    export default {
        name: "movimiento-bancario-create",
        data() {
            return {
                id_tipo_movimiento: '',
                id_cuenta: '',
                importe: '',
                impuesto: '',
                referencia: '',
                observaciones: '',
                cumplimiento: '',
                fecha: '',
                tiposMovimiento: [],
                cuentas: []
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
                return this.$store.dispatch('tesoreria/tipo-movimiento/index',{
                })
                    .then(data => {
                        this.tiposMovimiento = data.data;
                    })
            },

            getCuentas() {
                return this.$store.dispatch('cadeco/cuenta/index', {
                    params: {
                        include: 'empresa',
                        scope: 'paraTraspaso'
                    }
                })
                    .then(data => {
                        this.cuentas = data.data;
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
                        this.$emit('created')
                    })
            }
        },

        computed: {
            total() {
                let impuesto = this.impuesto ? parseFloat(this.impuesto) : 0;
                let importe = this.importe ? parseFloat(this.importe) : 0;
                return importe + impuesto;
            }
        }
    }
</script>