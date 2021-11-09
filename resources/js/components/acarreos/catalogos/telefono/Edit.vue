<template>
    <span>
        <button @click="init" type="button" class="btn btn-sm btn-outline-primary" title="Editar">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"></i> EDITAR TELÉFONO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
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
                                                    name="imei"
                                                    data-vv-as="Imei"
                                                    v-validate="{required: true, max:17, min:12, regex: /^([0-9]+)$/}"
                                                    class="form-control"
                                                    id="imei"
                                                    v-model="imei"
                                                    :class="{'is-invalid': errors.has('imei')}" />
                                            <div class="invalid-feedback" v-show="errors.has('imei')">{{ errors.first('imei') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group col-md-12 error-content">
                                            <label for="device_id" class="col-form-label">Id. Dispositivo:</label>
                                            <input
                                                    :disabled="edit_device"
                                                    type="text"
                                                    name="device_id"
                                                    data-vv-as="Id. Dispositivo"
                                                    v-validate="{required: true, max:20, regex: /^([a-zA-Z0-9]+)$/}"
                                                    class="form-control"
                                                    id="device_id"
                                                    v-model="device_id"
                                                    :class="{'is-invalid': errors.has('device_id')}" />
                                            <div class="invalid-feedback" v-show="errors.has('device_id')">{{ errors.first('device_id') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group col-md-12 error-content">
                                            <label for="marca" class="col-form-label">Marca:</label>
                                            <input
                                                type="text"
                                                name="marca"
                                                data-vv-as="Marca"
                                                v-validate="{required: true, max:20, regex: /^([a-zA-Z0-9 ]+)$/}"
                                                class="form-control"
                                                id="marca"
                                                v-model="marca"
                                                :class="{'is-invalid': errors.has('marca')}" />
                                            <div class="invalid-feedback" v-show="errors.has('marca')">{{ errors.first('marca') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group col-md-12 error-content">
                                            <label for="modelo" class="col-form-label">Modelo:</label>
                                            <input
                                                    type="text"
                                                    name="modelo"
                                                    data-vv-as="Modelo"
                                                    v-validate="{required: true, max:20, regex: /^([a-zA-Z0-9 ]+)$/}"
                                                    class="form-control"
                                                    id="modelo"
                                                    v-model="modelo"
                                                    :class="{'is-invalid': errors.has('modelo')}" />
                                            <div class="invalid-feedback" v-show="errors.has('modelo')">{{ errors.first('modelo') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group col-md-12 error-content">
                                            <label for="linea" class="col-form-label">Linea Telefónica 10 Dígitos:</label>
                                            <input
                                                type="text"
                                                name="linea"
                                                data-vv-as="Linea"
                                                v-validate="{required: true, max:10, min:10, regex:/^([0-9]+)$/}"
                                                class="form-control"
                                                id="linea"
                                                v-model="linea"
                                                :class="{'is-invalid': errors.has('linea')}" />
                                            <div class="invalid-feedback" v-show="errors.has('linea')">{{ errors.first('linea') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group col-md-12 error-content">
                                            <label for="checador" class="col-form-label">Checador</label>
                                            <model-list-select
                                                    id="checador"
                                                    v-validate="{required: true}"
                                                    name="checador"
                                                    data-vv-as="Checador"
                                                    v-model="id_checador"
                                                    option-value="id_usuario_intranet"
                                                    option-text="checador"
                                                    :list="checadores"
                                                    :placeholder="!cargando?'Seleccionar o buscar checador':'Cargando...'"
                                                    :class="{'is-invalid': errors.has('checador')}">
                                                </model-list-select>
                                            <div class="invalid-feedback" v-show="errors.has('checador')">{{ errors.first('checador') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
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
        name: "telefono-create",
        components: {ModelListSelect},
        props:['id'],
        data() {
            return {
                cargando:false,
                checadores:[],
                imei: '',
                device_id: '',
                marca: '',
                modelo: '',
                linea: '',
                id_checador: '',
                edit_device:false
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
                })
                .then((data) => {
                    this.imei = data.imei;
                    this.device_id = data.id_dispositivo;
                    this.marca = data.marca;
                    this.modelo = data.modelo;
                    this.linea = data.linea;
                    this.id_checador = data.id_checador;
                    if(data.id_dispositivo !== null){
                        this.edit_device = true;
                    }
                    this.getChecadores();
                });
            },
            getChecadores(){
                return this.$store.dispatch('acarreos/checador/getChecadores', {
                    params:{id_checador:this.id_checador}
                })
                    .then((data) => {
                        this.checadores = data.data;
                        this.cargando = false;
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update();
                    }
                });
            },
            update() {
                return this.$store.dispatch('acarreos/telefono/update', {
                    id:this.id,
                    params:{
                        imei: this.imei,
                        device_id: this.device_id,
                        marca: this.marca,
                        modelo: this.modelo,
                        linea: this.linea,
                        id_checador: this.id_checador,
                    }
                })
                .then((data) => {
                    this.$store.commit('acarreos/telefono/UPDATE_TELEFONO', data);
                    $(this.$refs.modal).modal('hide');
                });
            },
        }
    }
</script>

<style scoped>

</style>
