<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group error-content">
                            <label for="razon" class="col-form-label">Razón Social:</label>
                             <input class="form-control"
                                    name="razon"
                                    data-vv-as="RAZÓN SOCIAL"
                                    style="text-transform:uppercase;"
                                    v-model="registrar_empresa.razon_social"
                                    v-validate="{ required: true, min:6, max:255}"
                                    id="razon"
                                    :class="{'is-invalid': errors.has('razon')}"
                                    placeholder="RAZÓN SOCIAL" :maxlength="255"/>
                            <div class="invalid-feedback" v-show="errors.has('razon')">{{ errors.first('razon') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="rfc" class="col-form-label">RFC:</label>
                            <input class="form-control"
                                   name="rfc"
                                   data-vv-as="RFC"
                                   v-model="registrar_empresa.rfc"
                                   :class="{'is-invalid': errors.has('rfc')}"
                                   v-validate="{ required: true, regex: /^([A-ZÑ&]{3,4})(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]))([A-Z\d]{2})([A\d])$/ }"
                                   id="rfc" :maxlength="13"/>
                            <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="nss" class="col-form-label">Registro Patronal:</label>
                            <input class="form-control"
                                   name="nss"
                                   data-vv-as="'Registro Patronal'"
                                   v-model="registrar_empresa.nss"
                                   v-validate="{ length:11, min:11 }"
                                   id="nss"
                                   :class="{'is-invalid': errors.has('nss')}"
                                   placeholder="Registro Patronal" :maxlength="11"/>
                            <div class="invalid-feedback" v-show="errors.has('nss')">{{ errors.first('nss') }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate" v-if="$root.can('actualizar_expediente_proveedor', true)"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "TabDatosPrestadora",
        props: ['prestadora'],
        data(){
            return{
                registrar_empresa: {
                    'id' : '',
                    'rfc': '',
                    'razon_social' : '',
                    'nss' : '',
                    'rfc_proveedor' : '',
                    'id_proveedor' : '',
                    'rfc_prestadora' : ''
                },
                rfcValidate: false,
            }
        },
        mounted(){
            this.find();
        },
        methods: {
            invalidRFC() {
                this.rfcValidate = true;
            },
            find() {
                return this.$store.dispatch('padronProveedores/empresa/find', {
                    id: this.prestadora.id,
                    params: {include: ['proveedor']}
                }).then(data => {
                    this.registrar_empresa.id = data.id;
                    this.registrar_empresa.rfc = data.rfc;
                    this.registrar_empresa.razon_social = data.razon_social;
                    this.registrar_empresa.nss = data.nss;
                    this.registrar_empresa.rfc_proveedor = data.proveedor.rfc;
                    this.registrar_empresa.id_proveedor = data.proveedor.id;
                    this.registrar_empresa.rfc_prestadora = data.rfc;
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.revisarRFC();
                    }
                });
            },
            update() {
                this.registrar_empresa.razon_social = this.registrar_empresa.razon_social.toUpperCase();
                this.registrar_empresa.rfc =  this.registrar_empresa.rfc.toUpperCase();
                return this.$store.dispatch('padronProveedores/empresa/update', {
                    id: this.registrar_empresa.id,
                    data: this.$data.registrar_empresa,
                    params: {include: ['proveedor']}
                }).then((data) => {
                    this.registrar_empresa.id = data.id;
                    this.registrar_empresa.rfc = data.rfc;
                    this.registrar_empresa.razon_social = data.razon_social;
                    this.registrar_empresa.nss = data.nss;
                    this.registrar_empresa.id_proveedor = data.proveedor.id;
                    this.registrar_empresa.rfc_proveedor = data.proveedor.rfc;
                    this.registrar_empresa.rfc_prestadora = data.rfc;
                })
            },
            revisarRFC(){
                return this.$store.dispatch('padronProveedores/empresa/revisarRFC', {
                    id: this.prestadora.id,
                    data: {rfc : this.$data.registrar_empresa.rfc},
                }).then(data => {
                    if(data['mensaje']==false){
                        swal({
                            title: "¿Desea Reemplazar?",
                            text: "El RFC ingresado pertenece a la empresa prestadora ("+data['razon']+").",
                            icon: "warning",
                            buttons: {
                                cancel: {
                                    text: 'Cancelar',
                                    visible: true
                                },
                                confirm: {
                                    text: 'Si, Reemplazar',
                                    closeModal: false,
                                }
                            }
                        }).then((value) => {
                            if(value) {
                                this.update();
                            }
                        })
                    }else{
                        this.update();
                    }
                })
            }
        }
    }
</script>

<style scoped>

</style>
