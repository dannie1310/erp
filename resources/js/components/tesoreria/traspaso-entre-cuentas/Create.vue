<template>
    <span>
        <button @click="init" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar Traspaso
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR TRASPASO ENTRE CUENTAS</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Cuenta Origen -->
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_cuenta_origen">Cuenta Origen</label>
                                        <select
                                                class="form-control"
                                                name="id_cuenta_origen"
                                                data-vv-as="Cuenta Origen"
                                                id="id_cuenta_origen"
                                                v-model="id_cuenta_origen"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('id_cuenta_origen')}"
                                        >
                                            <option value>-- Cuenta Origen --</option>
                                            <option v-for="(item, index) in cuentasOrigen" :value="item.id">
                                                {{ `${item.numero} ${item.abreviatura } (${item.empresa.razon_social})` }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_cuenta_origen')">{{ errors.first('id_cuenta_origen') }}</div>
                                    </div>
                                </div>

                                <!-- Cuenta Destino -->
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_cuenta_destino">Cuenta Destino</label>
                                        <select
                                                class="form-control"
                                                name="id_cuenta_destino"
                                                data-vv-as="Cuenta Destino"
                                                id="id_cuenta_destino"
                                                v-model="id_cuenta_destino"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('id_cuenta_destino')}"
                                        >
                                            <option value>-- Cuenta Destino --</option>
                                            <option v-for="(item, index) in cuentasDestino" :value="item.id">
                                                {{ `${item.numero} ${item.abreviatura } (${item.empresa.razon_social})` }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_cuenta_destino')">{{ errors.first('id_cuenta_destino') }}</div>
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

                                <!-- Referencia -->
                                <div class="col-md-8">
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
        name: "traspaso-entre-cuentas-create",
        data() {
            return {
                id_cuenta_origen: '',
                id_cuenta_destino: '',
                fecha: '',
                cumplimiento: '',
                importe: '',
                referencia: '',
                observaciones: '',
                cuentas: []
            }
        },

        mounted() {
            this.getCuentas()
        },

        methods: {
            init() {
                $(this.$refs.modal).modal('show');
                this.id_cuenta_origen = '';
                this.id_cuenta_destino = '';
                this.fecha = '';
                this.cumplimiento = '';
                this.importe = '';
                this.referencia = '';
                this.observaciones = '';
                this.$validator.reset()
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
                return this.$store.dispatch('tesoreria/traspaso-entre-cuentas/store', this.$data)
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data)
                    })
            },
        },

        computed: {
            cuentasDestino() {
                return this.cuentas.filter(cuenta => {
                    return cuenta.id != this.id_cuenta_origen;
                })
            },

            cuentasOrigen() {
                return this.cuentas.filter(cuenta => {
                    return cuenta.id != this.id_cuenta_destino;
                })
            },
        }
    }
</script>