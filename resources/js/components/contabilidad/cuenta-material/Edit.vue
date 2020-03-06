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
                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE CUENTA DE MATERIAL</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="cuenta">Tipo de Cuenta</label>
                                        <select
                                                class="form-control"
                                                name="id_tipo_cuenta_material"
                                                id="id_tipo_cuenta_material"
                                                :value="cuenta.id_tipo_cuenta_material"
                                                @input="updateAttribute"
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
        name: "cuenta-material-edit",
        props: ['id'],
        data() {
            return {
                cargando: false
            }
        },
        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },

            tipos() {
                return this.$store.getters['contabilidad/tipo-cuenta-material/tipos']
            },

            cuenta() {
                return this.$store.getters['contabilidad/cuenta-material/currentCuenta']
            }
        },

        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('contabilidad/cuenta-material/SET_CUENTA', null)
                return this.$store.dispatch('contabilidad/cuenta-material/find', {
                    id: this.id
                })
                    .then(data => {
                        this.$store.commit('contabilidad/cuenta-material/SET_CUENTA', data)
                        $(this.$refs.modal).appendTo('body')
                        $(this.$refs.modal).modal('show');
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            update() {
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
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },

            updateAttribute(e) {
                return this.$store.commit('contabilidad/cuenta-material/UPDATE_ATTRIBUTE', {attribute: $(e.target).attr('name'), value: e.target.value})
            }
        }
    }
</script>
