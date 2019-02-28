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
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE CUENTA DE COSTO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" v-if="cuenta" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
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
                                                :value="cuenta.cuenta"
                                                @input="updateAttribute"
                                                :class="{'is-invalid': errors.has('cuenta')}">
                                        <div class="invalid-feedback" v-show="errors.has('cuenta')">{{ errors.first('cuenta') }}</div>
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
        name: "cuenta-costo-edit",
        props: ['id'],
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },

            cuenta() {
                return this.$store.getters['contabilidad/cuenta-costo/currentCuenta']
            }
        },

        methods: {
            find(id) {
                this.$store.commit('contabilidad/cuenta-costo/SET_CUENTA', null)
                return this.$store.dispatch('contabilidad/cuenta-costo/find', id)
                    .then(data => {
                        this.$store.commit('contabilidad/cuenta-costo/SET_CUENTA', data)
                        $(this.$refs.modal).modal('show');
                    })
            },

            update() {
                return this.$store.dispatch('contabilidad/cuenta-costo/update', this.cuenta)
                    .then(data => {
                        this.$store.dispatch('cadeco/costo/find', {
                            id: data.costo.id,
                            params: { include: 'cuenta' }
                        })
                            .then(data => {
                                $(this.$refs.modal).modal('hide');
                                this.$store.commit('cadeco/costo/UPDATE_COSTO', data);
                            });
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
                return this.$store.commit('contabilidad/cuenta-costo/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }
        }
    }
</script>