<template>
    <span>
        <!-- Button trigger modal -->
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" :data-target="'#cuenta-fondo-edit-modal' + id">
            <i class="fa fa-pencil"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" :id="'cuenta-fondo-edit-modal' + id" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE CUENTA DE FONDO</h5>
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
                                        <label for="fondo">Fondo</label>
                                        <input readonly type="text" class="form-control" id="fondo" v-model="cuenta.fondo.descripcion">
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
        name: "cuenta-fondo-edit",
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
                return this.$store.getters['contabilidad/cuenta-fondo/currentCuenta']
            }
        },

        methods: {
            find(id) {
                this.$store.dispatch('contabilidad/cuenta-fondo/find', id)
                    .then(() => {
                        this.loading = false;
                    })
            },

            update() {
                let self = this
                Swal({
                    title: 'Actualizar Cuenta de Fondo',
                    text: "¿Estás seguro?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, Actualizar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {
                        this.loading = true;
                        return self.$store.dispatch('contabilidad/cuenta-fondo/update', self.cuenta)
                            .then(() => {
                                $('.modal').modal('hide');
                                Swal({
                                    type: 'success',
                                    title: '¡Correcto!',
                                    text: 'Cuenta Actualizada correctamente',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }).then(() => {
                                this.loading = false;
                            })
                    }
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
                this.$store.commit('contabilidad/cuenta-fondo/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }
        }
    }
</script>