<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_cuenta_almacen')" class="btn btn-app btn-info float-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Registrar
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">REGISTRAR CUENTA DE ALMACÉN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div role="form">
                                <div class="form-group row error-content">
                                    <label for="almacen" class="col-sm-2 col-form-label">Almacén</label>
                                    <div class="col-sm-10">
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
                cuenta: '',
                cargando: false,
                almacenes: []
            }
        },

        methods: {
            init() {
                if (!this.datosContables) {
                    swal('¡Error!', 'No es posible registrar la cuenta debido a que no se ha configurado el formato de cuentas de la obra.', 'error')
                } else {
                    this.getAlmacenes()
                }
            },

            getAlmacenes() {
                this.$store.commit('cadeco/almacen/SET_ALMACENES', []);
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/index', {
                    params: { scope: ['sinCuenta', 'tipoMaterial'] }
                })
                    .then(data => {
                        this.almacenes = data.data
                        if (this.almacenes.length) {
                            $(this.$refs.modal).appendTo('body')
                            $(this.$refs.modal).modal('show');

                            this.id_almacen = '';
                            this.cuenta = '';

                            this.$validator.reset()
                        } else {
                            swal("Todos los almacénes tienen una cuenta registrada", "", "warning");
                        }
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },

            store() {
                return this.$store.dispatch('contabilidad/cuenta-almacen/store', this.$data)
                    .then(data => {
                        this.almacenes = this.almacenes.filter(almacen => {
                            return almacen.id != this.id_almacen;
                        });
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data)
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
            datosContables() {
                return this.$store.getters['auth/datosContables']
            }
        }
    }
</script>
