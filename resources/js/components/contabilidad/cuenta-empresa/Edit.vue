<template>
    <span>
        <!-- Button trigger modal -->
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" :data-target="'#cuenta-empresa-edit-modal' + id">
            <i class="fa fa-pencil"></i>
        </button>

        <!-- Modal -->
        <div class="modal fade" :id="'cuenta-empresa-edit-modal' + id" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE CUENTA DE EMPRESA</h5>
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
                                                v-model="cuenta.cuenta"
                                                :class="{'is-invalid': errors.has('cuenta')}">
                                        <div class="invalid-feedback" v-show="errors.has('cuenta')">{{ errors.first('cuenta') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="empresa">Empresa</label>
                                        <input readonly type="text" class="form-control" id="empresa" v-model="cuenta.empresa.descripcion">
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
        name: "cuenta-empresa-edit",
        props: ['id'],
        data() {
            return {
                cuenta: null,
                loading: false
            }
        },

        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },

            currentCuenta() {
                return this.$store.getters['contabilidad/cuenta-empresa/currentCuenta']
            }
        },

        watch: {
            currentCuenta: {
                handler(currentCuenta) {
                    this.cuenta = JSON.parse(JSON.stringify(currentCuenta));
                },
                deep: true
            }
        },

        methods: {
            find(id) {
                this.$store.dispatch('contabilidad/cuenta-empresa/find', id)
            },

            update() {
                let self = this
                Swal({
                    title: 'Actualizar Cuenta de Empresa',
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
                    return self.$store.dispatch('contabilidad/cuenta-empresa/update', self.$data.cuenta)
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
            }
        }
    }
</script>