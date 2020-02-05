<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_cuenta_general')" class="btn btn-app btn-info float-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR CUENTA GENERAL</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div role="form">
                                <div class="form-group row error-content">
                                    <label for="id_int_tipo_cuenta_contable" class="col-sm-2 col-form-label">Tipo de Cuenta</label>
                                    <div class="col-sm-10">
                                        <select
                                                class="form-control"
                                                v-model="id_int_tipo_cuenta_contable"
                                                name="id_int_tipo_cuenta_contable"
                                                id="id_int_tipo_cuenta_contable"
                                                v-validate="{required: true}"
                                                data-vv-as="Tipo de Cuenta"
                                                :class="{'is-invalid': errors.has('id_int_tipo_cuenta_contable')}">
                                        >
                                            <option value>-- Tipo de Cuenta -- </option>
                                            <option v-for="tipo in tipos" :value="tipo.id">{{ tipo.descripcion }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_int_tipo_cuenta_contable')">{{ errors.first('id_int_tipo_cuenta_contable') }}</div>
                                    </div>
                                </div>
                                <div class="form-group row error-content">
                                    <label for="cuenta_contable" class="col-sm-2 col-form-label">Cuenta</label>
                                    <div class="col-sm-10">
                                        <input
                                                type="text"
                                                name="cuenta_contable"
                                                data-vv-as="cuenta_contable"
                                                v-validate="{required: true, regex: datosContables}"
                                                class="form-control"
                                                v-mask="{regex: datosContables}"
                                                id="cuenta_contable"
                                                placeholder="Cuenta"
                                                v-model="cuenta_contable"
                                                :class="{'is-invalid': errors.has('cuenta_contable')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('cuenta_contable')">{{ errors.first('cuenta_contable') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Registrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
    export default {
        name: "cuenta-general-create",
        data() {
            return {
                cuenta_contable: '',
                id_int_tipo_cuenta_contable: '',
                tipos: [],
                cargando: false
            }
        },
        methods: {
            init() {
                if (!this.datosContables) {
                    swal('¡Error!', 'No es posible registrar la cuenta debido a que no se ha configurado el formato de cuentas de la obra.', 'error')
                } else {
                    this.getTipos()
                }
            },

            getTipos() {
                this.cargando = true;
                return this.$store.dispatch('contabilidad/tipo-cuenta-contable/index', {
                    config: {
                        params: {
                            scope: ['generales', 'sinCuenta']
                        }
                    }
                })
                    .then(data => {
                        this.tipos = data.data
                        if (this.tipos.length) {
                            $(this.$refs.modal).modal('show');

                            this.cuenta_contable = '';
                            this.id_int_tipo_cuenta_contable = '';

                            this.$validator.reset()
                        } else {
                            swal("No existen tipos de cuenta generales por registrar", "", "warning");
                        }
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },

            store() {
                return this.$store.dispatch('contabilidad/cuenta-general/store', this.$data)
                    .then(data => {
                        this.tipos = this.tipos.filter(tipo => {
                            return tipo.id != this.id_int_tipo_cuenta_contable;
                        });
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data)
                    });
            },


            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
        },

        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        }
    }
</script>
