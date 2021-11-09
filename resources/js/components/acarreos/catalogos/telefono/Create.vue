<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_telefono')" class="btn btn-app pull-right">
            <i class="fa fa-plus"></i> Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"></i> REGISTRAR TELÉFONO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group col-md-12 error-content">
                                        <label for="imei" class="col-form-label">Imei:</label>
                                        <input
                                                type="text"
                                                name="imei"
                                                data-vv-as="'Imei'"
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
                                                type="text"
                                                name="device_id"
                                                data-vv-as="'Id. Dispositivo'"
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
                                            data-vv-as="'Marca'"
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
                                                data-vv-as="'Modelo'"
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
                                            data-vv-as="Linea Telefónica"
                                            v-validate="{required: true, max:10, min:10, regex: /^([0-9]+)$/}"
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
                            <button type="submit" class="btn btn-primary" ><i class="fa fa-save"></i> Guardar</button>
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

            }
        },
        methods : {
            init() {
                this.getChecadores();
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            getChecadores(){
                return this.$store.dispatch('acarreos/checador/getChecadores', {})
                    .then((data) => {
                        this.checadores = data.data;
                        
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store();
                    }
                });
            },
            store() {
                return this.$store.dispatch('acarreos/telefono/store', {
                    imei: this.imei,
                    device_id: this.device_id,
                    marca: this.marca,
                    modelo: this.modelo,
                    linea: this.linea,
                    id_checador: this.id_checador,
                })
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created', data)
                    });
            },
        }
    }
</script>

<style scoped>

</style>
