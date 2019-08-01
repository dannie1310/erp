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
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE CUENTA DE FONDO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div role="form">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Fondo</label>
                                    <div class="col-sm-10">
                                        <p class="form-control">{{ cuenta.fondo.descripcion}}</p>
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
        name: "cuenta-fondo-edit",
        props: ['id'],
        data() {
            return {
                cargando: false,
                id_fondo: ""
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
            find() {
                this.$store.commit('contabilidad/cuenta-fondo/SET_CUENTA', null)
                this.cargando = true;
                return this.$store.dispatch('contabilidad/cuenta-fondo/find', {
                    id: this.id,
                    params: { include: 'fondo' }
                })
                    .then(data => {
                        this.$store.commit('contabilidad/cuenta-fondo/SET_CUENTA', data)
                        this.id_fondo = data.fondo.id
                        $(this.$refs.modal).modal('show');
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            update() {
                return this.$store.dispatch('contabilidad/cuenta-fondo/update', {
                    id: this.cuenta.id,
                    data: this.cuenta
                })
                    .then(data => {
                        this.$store.dispatch('cadeco/fondo/find', {
                            id: this.id_fondo,
                            params: { include: 'cuenta_fondo' }
                        })
                            .then(data => {
                                $(this.$refs.modal).modal('hide');
                                this.$store.commit('cadeco/fondo/UPDATE_FONDO', data);
                            });
                    })
            },


       /*     update() {
                return this.$store.dispatch('contabilidad/cuenta-material/update', {
                    id: this.id,
                    data: this.cuenta
                })
                    .then(data => {
                        this.$store.dispatch('cadeco/material/find', {
                            id: data.material.id,
                            params: { include: 'cuentaMaterial' }
                        })
                            .then(data => {
                                $(this.$refs.modal).modal('hide');
                                this.$store.commit('cadeco/material/UPDATE_MATERIAL', data);
                            });
                    })
            },*/


            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },

            updateAttribute(e) {
                return this.$store.commit('contabilidad/cuenta-fondo/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }
        }
    }
</script>