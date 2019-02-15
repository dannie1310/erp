<template>
    <span>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#add-movimiento">
            <i class="fa fa-plus"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="add-movimiento" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">AGREGAR MOVIMIENTO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_tipo_cuenta_contable">Tipo de Cuenta</label>
                                        <select
                                                class="form-control"
                                                id="id_tipo_cuenta_contable"
                                                name="id_tipo_cuenta_contable"
                                                v-model="movimiento.id_tipo_cuenta_contable"
                                                v-on:change="setCuentaContable"
                                                v-validate="{required: true, numeric: true}"
                                                data-vv-as="Tipo de Cuenta"
                                                :class="{'is-invalid': errors.has('id_tipo_cuenta_contable')}"
                                        >
                                            <option value>-- Tipo de Cuenta --</option>
                                            <option v-for="tipo in tiposCuentaContable" :value="tipo.id">{{ tipo.descripcion }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_tipo_cuenta_contable')">{{ errors.first('id_tipo_cuenta_contable') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cuenta_contable">Cuenta Contable</label>
                                        <input
                                                type="text"
                                                class="form-control"
                                                id="cuenta_contable"
                                                name="cuenta_contable"
                                                v-mask="{regex: datosContables}"
                                                v-model="movimiento.cuenta_contable"
                                                v-validate="{required: true, regex: datosContables}"
                                                data-vv-as="Cuenta Contable"
                                                :class="{'is-invalid': errors.has('cuenta_contable')}"
                                        />
                                        <div class="invalid-feedback" v-show="errors.has('cuenta_contable')">{{ errors.first('cuenta_contable') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="id_tipo_movimiento_poliza">Tipo</label>
                                        <select
                                                class="form-control"
                                                id="id_tipo_movimiento_poliza"
                                                name="id_tipo_movimiento_poliza"
                                                v-model="movimiento.id_tipo_movimiento_poliza"
                                                v-on:change="setTipoMovimiento"
                                                v-validate="{required: true}"
                                                data-vv-as="Tipo"
                                                :class="{'is-invalid': errors.has('id_tipo_movimiento_poliza')}"
                                        >
                                            <option value>-- Tipo --</option>
                                            <option value="1">Cargo</option>
                                            <option value="2">Abono</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_tipo_movimiento_poliza')">{{ errors.first('id_tipo_movimiento_poliza') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="importe">Importe</label>
                                        <input
                                                type="number"
                                                step="any"
                                                class="form-control"
                                                id="importe"
                                                name="importe"
                                                v-model="movimiento.importe"
                                                v-validate="{required: true, decimal: true}"
                                                data-vv-as="Importe"
                                                :class="{'is-invalid': errors.has('importe')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('importe')">{{ errors.first('importe') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="referencia">Referencia</label>
                                        <input
                                                type="text"
                                                class="form-control"
                                                id="referencia"
                                                name="referencia"
                                                v-model="movimiento.referencia"
                                                v-validate="{required: true, max: 100}"
                                                data-vv-as="Referencia"
                                                :class="{'is-invalid': errors.has('referencia')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="concepto">Concepto</label>
                                        <textarea
                                                class="form-control"
                                                id="concepto"
                                                name="concepto"
                                                v-model="movimiento.concepto"
                                                v-validate="{required: true, max: 4000}"
                                                data-vv-as="Concepto"
                                                :class="{'is-invalid': errors.has('concepto')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click="init">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "add-movimiento",
        data() {
            return {
                movimiento: {
                    concepto: '',
                    cuenta_contable: '',
                    tipoCuentaContable: {
                        id: '',
                        descripcion: ''
                    },
                    tipo: {
                        id: '',
                        descripcion: ''
                    },
                    importe: '',
                    referencia: '',
                    id_tipo_cuenta_contable: '',
                    id_tipo_movimiento_poliza: ''
                }
            }
        },

        mounted() {
            this.getTiposCuentaContable()
        },

        methods: {
            getTiposCuentaContable() {
                return this.$store.dispatch('contabilidad/tipo-cuenta-contable/index', {include: 'cuentaContable'})
            },

            setTipoMovimiento(e) {
                let id = $(e.target).val()
                if (id) {
                    if (id == 1) {
                        this.movimiento.tipo.id = 1
                        this.movimiento.tipo.descripcion = 'Cargo'
                    } else if (id == 2) {
                        this.movimiento.tipo.id = 2
                        this.movimiento.tipo.descripcion = 'Abono'
                    }
                } else {
                    this.movimiento.tipo.id = ''
                    this.movimiento.tipo.descripcion = ''
                }
            },

            setCuentaContable(e) {
                let id = $(e.target).val()
                if (id) {
                    let tipo = this.tiposCuentaContable.find(function(tipo) {
                        return tipo.id == id;
                    })

                    this.movimiento.cuenta_contable = tipo.cuentaContable ? tipo.cuentaContable.cuenta_contable : ''
                    this.movimiento.tipoCuentaContable.id = tipo.id
                    this.movimiento.tipoCuentaContable.descripcion = tipo.descripcion
                } else {
                    this.movimiento.cuenta_contable = ''
                    this.movimiento.tipoCuentaContable.id = ''
                    this.movimiento.tipoCuentaContable.descripcion = ''
                }

            },

            init() {
                this.movimiento = {
                    concepto: '',
                    cuenta_contable: '',
                    tipoCuentaContable: {
                        id: '',
                        descripcion: ''
                    },
                    tipo: {
                        id: '',
                        descripcion: ''
                    },
                    importe: '',
                    referencia: '',
                    id_tipo_cuenta_contable: '',
                    id_tipo_movimiento_poliza: ''
                }
                this.$validator.reset()
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.add()
                    }
                });
            },

            add() {
                $('#add-movimiento').modal('hide');
                this.$emit('add', this.movimiento)
                this.init();
            }
        },

        computed: {
            tiposCuentaContable() {
                return this.$store.getters['contabilidad/tipo-cuenta-contable/tipos']
            },
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        }
    }
</script>