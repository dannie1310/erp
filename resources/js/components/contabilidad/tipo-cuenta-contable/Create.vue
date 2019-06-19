<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_tipo_cuenta_contable')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar Tipo de Cuenta Contable
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR TIPO DE CUENTA CONTABLE</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="descripcion">Descripción de Tipo Cuenta Contable</label>
                                        <input
                                                type="text"
                                                name="descripcion"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="descripcion"
                                                placeholder="Descripcion"
                                                v-model="descripcion"
                                        >
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_naturaleza_poliza">Naturaleza de Cuenta</label>
                                        <select
                                                name="id_naturaleza_poliza"
                                                id="id_naturaleza_poliza"
                                                data-vv-as="Naturaleza"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                v-model="id_naturaleza_poliza"
                                                :class="{'is-invalid': errors.has('id_naturaleza_poliza')}"
                                        >
                                            <option value>-- Seleccione --</option>
                                            <option v-for="item in naturalezas" :value="item.id">{{ item.descripcion }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_naturaleza_poliza')">{{ errors.first('id_naturaleza_poliza') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" @click="validate">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
    export default {
        name: "tipo-cuenta-contable-create",
        data() {
            return {
                descripcion: '',
                id_naturaleza_poliza: ''
            }
        },

        computed: {
            naturalezas() {
                return this.$store.getters['contabilidad/naturaleza-poliza/naturalezas'];
            },

            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },

        methods: {
            init() {
                $(this.$refs.modal).modal('show');

                this.id_naturaleza_poliza = '';
                this.descripcion = '';

                this.$validator.reset()
            },

            store() {
                return this.$store.dispatch('contabilidad/tipo-cuenta-contable/store', this.$data)
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