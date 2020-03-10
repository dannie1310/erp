<template>
    <span>
        <!-- Button trigger modal -->
        <button @click="find()" type="button" class="btn btn-sm btn-outline-info" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-pencil" v-else></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" v-if="cuenta">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE CUENTA DE COSTO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label>Costo</label>
                                        <p class="form-control">{{ cuenta.costo.descripcion }}</p>
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
                                                v-model="cuenta.cuenta"
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
        data() {
            return {
                cuenta: null,
                cargando: false
            }
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        },

        methods: {
            find() {
                this.cargando = true;
                return this.$store.dispatch('contabilidad/cuenta-costo/find', {
                    id: this.id
                })
                    .then(data => {
                        this.cuenta = data;
                        $(this.$refs.modal).appendTo('body')
                        $(this.$refs.modal).modal('show');
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            update() {
                return this.$store.dispatch('contabilidad/cuenta-costo/update', {
                    id: this.id,
                    data: this.cuenta
                })
                    .then(data => {
                        $(this.$refs.modal).modal('hide');
                        this.$store.commit('contabilidad/cuenta-costo/UPDATE_CUENTA', data);
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            }
        }
    }
</script>
