<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-info" :disabled="cargando">
            <i class="fa fa-pencil" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>


        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" v-if="cuenta">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE CUENTA GENERAL</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">


                            <div role="form">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Tipo</label>
                                    <div class="col-sm-10">
                                        <p class="form-control">{{ cuenta.tipo.descripcion}}</p>
                                    </div>
                                </div>
                                <div class="form-group row error-content">
                                    <label for="cuenta" class="col-sm-2 col-form-label">Cuenta Contable</label>
                                    <div class="col-sm-10">
                                        <input
                                                type="text"
                                                name="cuenta_contable"
                                                data-vv-as="Cuenta Contable"
                                                v-validate="{required: true, regex: datosContables}"
                                                class="form-control"
                                                v-mask="{regex: datosContables}"
                                                id="cuenta"
                                                placeholder="Cuenta Contable"
                                                :value="cuenta.cuenta_contable"
                                                @input="updateAttribute"
                                                :class="{'is-invalid': errors.has('cuenta_contable')}">
                                        <div class="invalid-feedback" v-show="errors.has('cuenta_contable')">{{ errors.first('cuenta_contable') }}</div>
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
        name: "cuenta-tipo-edit",
        props: ['id'],
        data() {
            return {
                cargando: false,
            }
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },

            cuenta() {
                return this.$store.getters['contabilidad/cuenta-general/currentCuenta']
            }
        },

        methods: {
            find() {
                this.$store.commit('contabilidad/cuenta-general/SET_CUENTA', null)
                this.cargando = true;
                return this.$store.dispatch('contabilidad/cuenta-general/find', {
                    id: this.id
                })
                    .then(data => {
                        this.$store.commit('contabilidad/cuenta-general/SET_CUENTA', data)
                        $(this.$refs.modal).appendTo('body')
                        $(this.$refs.modal).modal('show');
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            update() {
                return this.$store.dispatch('contabilidad/cuenta-general/update', {
                    id: this.cuenta.id,
                    data: this.cuenta
                })
                    .then(data => {
                        this.$store.commit('contabilidad/cuenta-general/UPDATE_CUENTA', data);
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
                return this.$store.commit('contabilidad/cuenta-general/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }
        }
    }
</script>
