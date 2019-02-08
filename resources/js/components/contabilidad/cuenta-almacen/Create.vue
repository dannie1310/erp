<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_cuenta_almacen')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar Cuenta
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR CUENTA DE ALMACÉN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_almacen">Almacén</label>
                                        <select
                                                name="id_almacen"
                                                id="id_almacen"
                                                data-vv-as="Almacén"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                v-model="id_almacen"
                                                :class="{'is-invalid': errors.has('id_almacen')}"
                                        >
                                            <option value>-- Almacén --</option>
                                            <option v-for="item in almacenes" :value="item.id">{{ item.descripcion }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_almacen')">{{ errors.first('id_almacen') }}</div>
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
                                                v-model="cuenta"
                                                :class="{'is-invalid': errors.has('cuenta')}">
                                        <div class="invalid-feedback" v-show="errors.has('cuenta')">{{ errors.first('cuenta') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
    export default {
        name: "cuenta-almacen-create",
        data() {
            return {
                id_almacen: '',
                cuenta: ''
            }
        },

        mounted() {
            this.getAlmacenes();
        },

        methods: {
            init() {
                $(this.$refs.modal).modal('show');

                this.id_almacen = '';
                this.cuenta = '';

                this.$validator.reset()
            },

            getAlmacenes() {
                return this.$store.dispatch('cadeco/almacen/index', {
                    params: { scope: 'sinCuenta' }
                })
            },

            store() {
                return this.$store.dispatch('contabilidad/cuenta-almacen/store', this.$data)
                    .then(() => {
                        this.$store.commit('cadeco/almacen/SET_ALMACENES', this.almacenes.filter(almacen => {
                            return almacen.id != this.id_almacen;
                        }));
                        $(this.$refs.modal).modal('hide');
                    });
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
        },

        computed: {
            almacenes() {
                return this.$store.getters['cadeco/almacen/almacenes'];
            },

            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        }
    }
</script>