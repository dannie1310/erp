<template>
    <span>
        <!-- Button trigger modal -->
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-info">
            <i class="fa fa-pencil"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDITAR MOVIMIENTO BANCARIO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" v-if="movimiento" @submit.prevent="validate">
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
                                                :value="movimiento.id_tipo_movimiento"
                                                @input="updateAttribute"
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
                                                :value="movimiento.id_cuenta"
                                                @input="updateAttribute"
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
                                                :value="movimiento.importe"
                                                @input="updateAttribute"
                                                v-validate="{required: true, decimal: true}"
                                                data-vv-as="Importe"
                                                :class="{'is-invalid': errors.has('importe')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('importe')">{{ errors.first('importe') }}</div>
                                    </div>
                                </div>

                                <!-- Impuesto -->
                                <div class="col-md-4" v-if="movimiento.id_tipo_movimiento == 4">
                                    <div class="form-group error-content">
                                        <label for="impuesto">Impuesto</label>
                                        <input
                                                type="number"
                                                step="any"
                                                name="impuesto"
                                                id="impuesto"
                                                class="form-control"
                                                :value="movimiento.impuesto"
                                                @input="updateAttribute"
                                                v-validate="{decimal: true}"
                                                data-vv-as="Impuesto"
                                                :class="{'is-invalid': errors.has('impuesto')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('impuesto')">{{ errors.first('impuesto') }}</div>
                                    </div>
                                </div>

                                <!-- Total -->
                                <div class="col-md-4" v-if="movimiento.id_tipo_movimiento == 4">
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
                                                name="transaccion.referencia"
                                                id="referencia"
                                                class="form-control"
                                                :value="movimiento.transaccion.referencia"
                                                @input="updateAttribute"
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
                                                :value="movimiento.fecha"
                                                @input="updateAttribute"
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
                                                name="transaccion.cumplimiento"
                                                id="cumplimiento"
                                                class="form-control"
                                                :value="movimiento.transaccion.cumplimiento"
                                                @input="updateAttribute"
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
                                                :value="movimiento.observaciones"
                                                @input="updateAttribute"
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
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "movimiento-bancario-edit",
        props: ['id'],
        data() {
            return {
                tiposMovimiento: [],
                cuentas: []
            }
        },

        computed: {
            movimiento() {
                return (this.$store.getters['tesoreria/movimiento-bancario/currentMovimiento'] != null && this.$store.getters['tesoreria/movimiento-bancario/currentMovimiento'].id == this.id) ?  this.$store.getters['tesoreria/movimiento-bancario/currentMovimiento'] : null
            },

            total() {
                let impuesto = this.movimiento.impuesto ? parseFloat(this.movimiento.impuesto) : 0;
                let importe = this.movimiento.importe ? parseFloat(this.movimiento.importe) : 0;
                return importe + impuesto;
            }
        },

        methods: {
            getTiposMovimiento() {
                return this.$store.dispatch('tesoreria/tipo-movimiento/index')
                    .then(data => {
                        this.tiposMovimiento = data.data;
                    });
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
                    });
            },

            find(id) {
                axios.all([
                    this.getTiposMovimiento(),
                    this.getCuentas()
                ])
                    .then(() => {
                        this.$store.dispatch('tesoreria/movimiento-bancario/find', {
                            id: id,
                            params: { include: 'transaccion' }
                        })
                            .then(data => {
                                this.$store.commit('tesoreria/movimiento-bancario/SET_MOVIMIENTO', data);
                                $(this.$refs.modal).modal('show');
                            })
                    });
            },

            update() {
                return this.$store.dispatch('tesoreria/movimiento-bancario/update', {
                    id: this.id,
                    data: {
                        id_tipo_movimiento: this.movimiento.id_tipo_movimiento,
                        id_cuenta: this.movimiento.id_cuenta,
                        importe: this.movimiento.importe,
                        impuesto: this.movimiento.impuesto,
                        referencia: this.movimiento.transaccion.referencia,
                        observaciones: this.movimiento.observaciones,
                        cumplimiento: this.movimiento.transaccion.cumplimiento,
                        fecha: this.movimiento.fecha
                    },
                    params: { include: 'cuenta.empresa,transaccion' }
                })
                    .then(data => {
                        this.$store.commit('tesoreria/movimiento-bancario/UPDATE_MOVIMIENTO', data);
                        $(this.$refs.modal).modal('hide');
                    })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },

            updateAttribute(e) {
                return this.$store.commit('tesoreria/movimiento-bancario/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }
        }
    }
</script>