<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-info" :disabled="cargando">
            <i class="fa fa-pencil" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE CUENTA DE ALMACÉN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" v-if="cuenta" @submit.prevent="validate">
                        <div class="modal-body">
                            <div role="form">
                                <div class="form-group row">
                                    <label for="almacen" class="col-sm-2 col-form-label">Almacen</label>
                                    <div class="col-sm-10">
                                        <p class="form-control">{{ cuenta.almacen.descripcion}}</p>
                                    </div>
                                </div>
                                <div class="form-group row error-content">
                                    <label for="cuenta" class="col-sm-2 col-form-label">Cuenta</label>
                                    <div class="col-sm-10">
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
        name: "cuenta-almacen-edit",
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
                return this.$store.getters['contabilidad/cuenta-almacen/currentCuenta']
            }
        },

        methods: {
            find(id) {
                this.$store.commit('contabilidad/cuenta-almacen/SET_CUENTA', null)
                this.cargando = true;
                return this.$store.dispatch('contabilidad/cuenta-almacen/find', {
                    id: id
                })
                    .then(data => {
                        this.$store.commit('contabilidad/cuenta-almacen/SET_CUENTA', data)
                        $(this.$refs.modal).appendTo('body')
                        $(this.$refs.modal).modal('show');
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            update() {
                return this.$store.dispatch('contabilidad/cuenta-almacen/update', {
                    id: this.cuenta.id,
                    data: this.cuenta
                })
                    .then(data => {
                        this.$store.commit('contabilidad/cuenta-almacen/UPDATE_CUENTA', data);
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
                return this.$store.commit('contabilidad/cuenta-almacen/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }
        }
    }
</script>
