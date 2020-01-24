<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_cuenta_fondo')" class="btn btn-app btn-info float-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR CUENTA DE FONDO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_fondo">Fondo</label>
                                        <fondo-select
                                                scope="sinCuenta"
                                                name="id_fondo"
                                                id="id_fondo"
                                                data-vv-as="Fondo"
                                                v-validate="{required: true}"
                                                v-model="id_fondo"
                                                :error="errors.has('id_fondo')">
                                        ></fondo-select>
                                        <div class="error-label" v-show="errors.has('id_fondo')">{{ errors.first('id_fondo') }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="cuenta">Cuenta</label>
                                        <input
                                                type="text"
                                                name="cuenta"
                                                data-vv-as="Cuenta"
                                                v-validate="{required: true, regex: datosContables}"
                                                class="form-control"
                                                v-mask="{regex: datosContables}"
                                                id="cuenta"
                                                placeholder="Cuenta"
                                                v-model="cuenta"
                                                :class="{'is-invalid': errors.has('cuenta')}">
                                        <div class="error-label" v-show="errors.has('cuenta')">{{ errors.first('cuenta') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import FondoSelect from "../../cadeco/fondo/Select";
    export default {
        name: "cuenta-fondo-create",
        components: {FondoSelect},
        data() {
            return {
                id_fondo: '',
                cuenta: '',
                cargando: false
            }
        },

        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },

        methods: {
            init() {
                if (!this.datosContables) {
                    swal('¡Error!', 'No es posible registrar la cuenta debido a que no se ha configurado el formato de cuentas de la obra.', 'error')
                } else {
                    this.cargando = true;
                    $(this.$refs.modal).modal('show');

                    this.id_fondo = '';
                    this.cuenta = '';

                    this.$validator.reset()
                    this.cargando = false
                }
            },

            store() {
                return this.$store.dispatch('contabilidad/cuenta-fondo/store', this.$data)
                    .then(data => {
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
            }
        }
    }
</script>

<style scoped>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>
