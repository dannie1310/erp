<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="Desactivar">
            <i class="fa fa-close"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-close"></i> DESACTIVAR OPERADOR</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="!operador">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Nombre:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <h6>{{operador.nombre}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Dirección:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <h6>{{operador.direccion}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <h6><b>Número de Licencia:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <h6>{{operador.no_licencia}}</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <h6><b>Vigencia de Licencia:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <h6>{{operador.licencia_vigencia_format}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <h6><b>Fecha Registro:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <h6>{{operador.fecha_registro_format}}</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <h6><b>Estatus:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <span class="badge" :style="{'background-color': operador.estado_color}">{{ operador.estado_format }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Registró:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <h6>{{operador.usuario_registro}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card" v-if="operador.estado == 1">
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <label for="motivo" class="col-md-2 col-form-label">Motivo:</label>
                                            <div class="col-md-10">
                                                <textarea
                                                    name="motivo"
                                                    id="motivo"
                                                    class="form-control"
                                                    v-model="motivo"
                                                    v-validate="{required: true}"
                                                    data-vv-as="Motivo"
                                                    :class="{'is-invalid': errors.has('motivo')}"
                                                ></textarea>
                                                <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir" v-if="operador"><i class="fa fa-close"></i>Cerrar</button>
                        <button type="submit" class="btn btn-primary" @click="validate" :disabled="errors.count() > 0" v-if="operador">
                            <i class="fa fa-save"></i>Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
      </span>
</template>

<script>
    export default {
        name: "operador-desactivar",
        props: ['id'],
        data() {
            return {
                motivo : '',
                carga : false
            }
        },
        methods: {
            salir() {
                $(this.$refs.modal).modal('hide');
            },
            find() {
                this.motivo = '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('acarreos/operador/SET_OPERADOR', null);
                return this.$store.dispatch('acarreos/operador/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('acarreos/operador/SET_OPERADOR', data);
                    this.carga = true;
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.desactivar()
                    }
                });
            },
            desactivar() {
                return this.$store.dispatch('acarreos/operador/desactivar', {
                    id: this.id,
                    params: {motivo: this.motivo}})
                    .then((data) => {
                        this.$store.commit('acarreos/operador/UPDATE_OPERADOR', data);
                        $(this.$refs.modal).modal('hide');
                    })
            }
        },
        computed: {
            operador() {
                return this.$store.getters['acarreos/operador/currentOperador']
            }
        }
    }
</script>

<style scoped>

</style>
