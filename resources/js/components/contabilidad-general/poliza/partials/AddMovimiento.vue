<template>
    <span>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#add-movimiento" :disabled="cargando" @click="abrir">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
        </button>

          <!-- Modal -->
        <div class="modal fade" id="add-movimiento" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"/>AGREGAR MOVIMIENTO</h5>
                        <button type="button" class="close" data-dismiss="modal" v-on:click="init" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body"v-if="cargando">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label>Cuenta</label>
                                         <model-list-select
                                             name="cuenta"
                                             placeholder="Seleccionar o buscar por código o nombre de la cuenta"
                                             data-vv-as="Cuenta"
                                             v-model="id_cuenta"
                                             option-value="id"
                                             v-validate="{required: true}"
                                             :custom-text="cuentaDescripcion"
                                             :list="cuentas"
                                             :class="{'is-invalid': errors.has('cuenta')}"
                                         />
                                        <div class="invalid-feedback" v-show="errors.has('cuenta')">
                                            {{ errors.first('cuenta') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Tipo</label>
                                        <select
                                            class="form-control"
                                            name="tipo"
                                            v-model="movimiento.tipo"
                                            v-validate="{required: true}"
                                            data-vv-as="Tipo"
                                            :class="{'is-invalid': errors.has('tipo')}"
                                        >
                                            <option value>-- Tipo --</option>
                                            <option value="0">Cargo</option>
                                            <option value="1">Abono</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="importe">Importe</label>
                                        <input
                                            type="number"
                                            step="any"
                                            class="form-control"
                                            id="importe"
                                            name="importe"
                                            v-model="movimiento.importe"
                                            v-validate="{required: true, decimal: true}"
                                            data-vv-as="Importe"
                                            :class="{'is-invalid': errors.has('importe')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('importe')">{{ errors.first('importe') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="referencia">Referencia</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="referencia"
                                            name="referencia"
                                            v-model="movimiento.referencia"
                                            v-validate="{required: true, max: 100}"
                                            data-vv-as="Referencia"
                                            :class="{'is-invalid': errors.has('referencia')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="concepto">Concepto</label>
                                        <textarea
                                            class="form-control"
                                            id="concepto"
                                            name="concepto"
                                            v-model="movimiento.concepto"
                                            v-validate="{required: true, max: 4000}"
                                            data-vv-as="Concepto"
                                            :class="{'is-invalid': errors.has('concepto')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click="init">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "AddMovimiento",
        props: ['id_empresa', 'cuentas'],
        components: {ModelListSelect},
        data() {
            return {
                movimiento: {
                    concepto: '',
                    cuenta: {
                        id: '',
                        descripcion: '',
                        cuenta: '',
                    },
                    importe: '',
                    referencia: '',
                    tipo: ''
                },
                id_cuenta: '',
                cargando: false
            }
        },
        mounted() {

        },
        methods: {
            abrir(){
                this.cargando = true
                $('#add-movimiento').modal('show');
            },
            cuentaDescripcion (item) {
                return `[${item.cuenta}]-[${item.descripcion}]`
            },
            init() {
                this.id_cuenta = ''
                this.movimiento = {
                    concepto: '',
                    cuenta: {
                        id: '',
                        descripcion: '',
                        cuenta: '',
                    },
                    importe: '',
                    referencia: '',
                    tipo: '',
                }
                this.cargando = false;
                this.$validator.reset()
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.movimiento.importe = parseFloat(this.movimiento.importe)
                        this.movimiento.tipo = parseInt(this.movimiento.tipo)
                        this.add()
                    }
                });
            },

            add() {
                $('#add-movimiento').modal('hide');
                this.$emit('add', this.movimiento)
                this.init();
            },
        },
        watch: {
            id_cuenta(value) {
                if (value) {
                    this.cuentas.map(cuenta => {
                        if (value === cuenta.id ) {
                            this.movimiento.cuenta.id = cuenta.id
                            this.movimiento.cuenta.cuenta = cuenta.cuenta
                            this.movimiento.cuenta.descripcion = cuenta.descripcion;
                        }
                    });
                }
            }
        }
    }
</script>

<style scoped>

</style>
