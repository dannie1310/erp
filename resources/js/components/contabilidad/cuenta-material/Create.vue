<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_cuenta_material')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i> Registrar Cuenta
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR CUENTA DE MATERIAL</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="id_material">Material</label>
                                        <material-select
                                                name="id_material"
                                                data-vv-as="Material"
                                                v-validate="{required: true}"
                                                id="id_material"
                                                v-model="id_material"
                                                :error="errors.has('id_material')"
                                                scope="sinCuenta"
                                                ref="selectMaterial"
                                                :disableBranchNodes="false"
                                        ></material-select>
                                        <div class="error-label" v-show="errors.has('id_material')">{{ errors.first('id_material') }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="cuenta">Tipo de Cuenta</label>
                                        <select
                                                class="form-control"
                                                name="id_tipo_cuenta_material"
                                                id="id_tipo_cuenta_material"
                                                v-model="id_tipo_cuenta_material"
                                                data-vv-as="Tipo de Cuenta"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('id_tipo_cuenta_material')}">
                                        >
                                            <option value>-- Tipo de Cuenta --</option>
                                            <option v-for="tipo in tipos" :value="tipo.id">{{ tipo.descripcion }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_tipo_cuenta_material')">{{ errors.first('id_tipo_cuenta_material') }}</div>
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
                                                :class="{'is-invalid': errors.has('cuenta')}"

                                        >
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
    import MaterialSelect from "../../cadeco/material/Select";
    export default {
        name: "cuenta-material-create",
        components: {MaterialSelect},
        data() {
            return {
                id_material: '',
                id_tipo_cuenta_material: '',
                cuenta: '',
                cargando: false
            }
        },

        methods: {
            init() {
                if (!this.datosContables) {
                    swal('¡Error!', 'No es posible registrar la cuenta debido a que no se ha configurado el formato de cuentas de la obra.', 'error')
                } else {
                    this.cargando = true;
                    $(this.$refs.modal).modal('show');

                    this.id_material = '';
                    this.id_tipo_cuenta_material = '';
                    this.cuenta = '';

                    this.$validator.reset()
                    this.cargando = false;
                }
            },

            store() {
                return this.$store.dispatch('contabilidad/cuenta-material/store', this.$data)
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data);
                        this.$refs.selectMaterial.getRootNodes();
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            }
        },

        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },

            tipos() {
                return this.$store.getters['contabilidad/tipo-cuenta-material/tipos'];
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