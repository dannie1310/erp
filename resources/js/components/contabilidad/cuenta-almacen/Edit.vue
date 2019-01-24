<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" :data-target="'#cuenta-almacen-edit-modal' + id">
            <i class="fa fa-pencil"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" :id="'cuenta-almacen-edit-modal' + id" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE CUENTA DE ALMACÉN</h5>
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="almacen">Almacen</label>
                                        <input readonly type="text" class="form-control" id="almacen" :value="cuenta.almacen.descripcion">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="loading">Guardar Cambios</button>
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
                loading: true
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
                this.$store.dispatch('contabilidad/cuenta-almacen/find', id)
                    .then(() => {
                        this.loading = false;
                    })
            },

            update() {
                let self = this

                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Cuenta de Almacén",
                    icon: "warning",
                    buttons: ['Cancelar', 'Si, Actualizar']
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            this.loading = true;
                            return self.$store.dispatch('contabilidad/cuenta-almacen/update', self.cuenta)
                                .then(() => {
                                    $('.modal').modal('hide');
                                    swal("Cuenta actualizada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    });
                                }).then(() => {
                                    this.loading = false;
                                })
                        }
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },

            updateAttribute(e) {
                this.$store.commit('contabilidad/cuenta-almacen/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }
        }
    }
</script>