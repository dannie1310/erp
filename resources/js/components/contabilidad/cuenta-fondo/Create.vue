<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_cuenta_fondo')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar Cuenta
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
                                        <fondo-autocomplete scope="sinCuenta"
                                                name="id_fondo"
                                                id="id_fondo"
                                                data-vv-as="Fondo"
                                                v-validate="{required: true}"
                                                v-model="id_fondo"
                                                :class="{'is-invalid': errors.has('id_fondo')}">
                                        ></fondo-autocomplete>
                                        <div class="invalid-feedback" v-show="errors.has('id_fondo')">{{ errors.first('id_fondo') }}</div>
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
                                        <div class="invalid-feedback" v-show="errors.has('cuenta')">{{ errors.first('cuenta') }}</div>
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
    import FondoAutocomplete from "../../cadeco/fondo/Autocomplete";
    export default {
        name: "cuenta-fondo-create",
        components: {FondoAutocomplete},
        data() {
            return {
                id_fondo: '',
                cuenta: ''
            }
        },

        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },

        methods: {
            init() {
                $(this.$refs.modal).modal('show');

                this.id_fondo = '';
                this.cuenta = '';

                this.$validator.reset()
            },

            store() {
                return this.$store.dispatch('contabilidad/cuenta-fondo/store', this.$data)
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data);
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