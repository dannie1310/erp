<template>
    <span>
        <button @click="init" type="button" class="btn btn-sm btn-outline-danger" title="Desactivar">
            <i class="fa fa-close"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-close"></i> DESACTIVAR TELÉFONO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="cargando">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group col-md-12 error-content">
                                        <label for="imei" class="col-form-label">Imei:</label>
                                        <input
                                            :disabled="true"
                                            type="text"
                                            name="Imei"
                                            data-vv-as="'Imei'"
                                            class="form-control"
                                            id="imei"
                                            v-model="telefono.imei" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group col-md-12 error-content">
                                        <label for="device_id" class="col-form-label">Id. Dispositivo:</label>
                                        <input
                                            :disabled="true"
                                            type="text"
                                            name="Id. Dispositivo"
                                            data-vv-as="'Id. Dispositivo'"
                                            class="form-control"
                                            id="device_id"
                                            v-model="telefono.id_dispositivo" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group col-md-12 error-content">
                                        <label for="marca" class="col-form-label">Marca:</label>
                                        <input
                                            :disabled="true"
                                            type="text"
                                            name="Marca"
                                            data-vv-as="'Marca'"
                                            class="form-control"
                                            id="marca"
                                            v-model="telefono.marca" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group col-md-12 error-content">
                                        <label for="modelo" class="col-form-label">Modelo:</label>
                                        <input
                                            :disabled="true"
                                            type="text"
                                            name="Modelo"
                                            data-vv-as="'Modelo'"
                                            class="form-control"
                                            id="modelo"
                                            v-model="telefono.modelo"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group col-md-12 error-content">
                                        <label for="linea" class="col-form-label">Linea Telefónica:</label>
                                        <input
                                            :disabled="true"
                                            type="text"
                                            name="LINEA TELEFÓNICA"
                                            data-vv-as="'LINEA TELEFÓNICA'"
                                            class="form-control"
                                            id="linea"
                                            v-model="telefono.linea" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group col-md-12 error-content">
                                        <label for="checador" class="col-form-label">Checador</label>
                                        <input
                                            :disabled="true"
                                            type="text"
                                            name="Checador"
                                            data-vv-as="'Checador'"
                                            class="form-control"
                                            id="checador"
                                            v-model="telefono.checador" />
                                    </div>
                                </div>
                            </div>
                            <div class="card">
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="validate"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "telefono-desactivar",
        props:['id'],
        data() {
            return {
                cargando:false,
                motivo:''
            }
        },
        methods : {
            init() {
                this.cargando = true;
                this.getTelefono();
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            getTelefono(){
                return this.$store.dispatch('acarreos/telefono/find', {
                    id:this.id,
                    params:{include:'historicos'}
                })
                .then((data) => {
                    this.$store.commit('acarreos/telefono/SET_TELEFONO', data);
                    this.cargando = false;
                });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.desactivar();
                    }
                });
            },
            desactivar() {
                return this.$store.dispatch('acarreos/telefono/desactivar', {
                    id:this.id,
                    params:{
                        motivo: this.motivo,
                    }
                })
                .then((data) => {
                    this.$store.commit('acarreos/telefono/UPDATE_TELEFONO', data);
                    $(this.$refs.modal).modal('hide');
                });
            }
        },
        computed: {
            telefono() {
                return this.$store.getters['acarreos/telefono/currentTelefono']
            }
        }
    }
</script>

<style scoped>

</style>
