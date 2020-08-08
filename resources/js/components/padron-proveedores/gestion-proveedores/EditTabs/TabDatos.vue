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
                                    v-model="razon"
                                    v-validate="{ required: true, max_value:234}"
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
                                   v-model="rfc"
                                   :class="{'is-invalid':rfcValidate}"
                                   v-validate="{ required: true, regex: /\.(js|ts)$/ }"
                                   id="rfc"
                                   placeholder="RFC" :maxlength="16"/>
                            <span class="text-danger" v-if="rfcValidate">RFC Inválido</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="nss" class="col-form-label">NSS:</label>
                            <input class="form-control"
                                   name="nss"
                                   data-vv-as="NSS"
                                   v-model="nss"
                                   v-validate="{ required: true }"
                                   id="nss"
                                   :class="{'is-invalid': errors.has('nss')}"
                                   placeholder="NSS"/>
                            <div class="invalid-feedback" v-show="errors.has('nss')">{{ errors.first('nss') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="contacto" class="col-form-label">Contacto:</label>
                            <input class="form-control"
                                   name="contacto"
                                   data-vv-as="CONTACTO"
                                   v-model="contacto"
                                   v-validate="{ required: true }"
                                   id="contacto"
                                   :class="{'is-invalid': errors.has('contacto')}"
                                   placeholder="CONTACTO"/>
                            <div class="invalid-feedback" v-show="errors.has('contacto')">{{ errors.first('contacto') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="telefono" class="col-form-label">Teléfono:</label>
                            <input class="form-control"
                                   name="telefono"
                                   data-vv-as="TELÉFONO"
                                   v-model="telefono"
                                   v-validate="{ required: true }"
                                   id="telefono"
                                   :class="{'is-invalid': errors.has('telefono')}"
                                   placeholder="TELÉFONO"/>
                            <div class="invalid-feedback" v-show="errors.has('telefono')">{{ errors.first('telefono') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="correo" class="col-form-label">Correo:</label>
                            <input class="form-control"
                                   name="correo"
                                   data-vv-as="CORREO"
                                   v-model="correo"
                                   v-validate="{ required: true }"
                                   id="correo"
                                   :class="{'is-invalid': errors.has('correo')}"
                                   placeholder="CORREO"/>
                            <div class="invalid-feedback" v-show="errors.has('correo')">{{ errors.first('correo') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="giro" class="col-form-label">Giro:</label>
                            <input class="form-control"
                                   name="giro"
                                   data-vv-as="GIRO"
                                   v-model="giro"
                                   v-validate="{ required: true }"
                                   id="giro"
                                   :class="{'is-invalid': errors.has('giro')}"
                                   placeholder="GIRO"/>
                            <div class="invalid-feedback" v-show="errors.has('giro')">{{ errors.first('giro') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="especialidad" class="col-form-label">Especialidad:</label>
                            <input class="form-control"
                                   name="especialidad"
                                   data-vv-as="ESPECIALIDAD"
                                   v-model="especialidad"
                                   v-validate="{ required: true }"
                                   id="especialidad"
                                   :class="{'is-invalid': errors.has('especialidad')}"
                                   placeholder="ESPECIALIDAD"/>
                            <div class="invalid-feedback" v-show="errors.has('especialidad')">{{ errors.first('especialidad') }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" :disabled="errors.count() > 0" @click="validate">Guardar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    const rfcRegex =/^([A-ZÑ&]{3,4}) ?(?:- ?)?(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])) ?(?:- ?)?([A-Z\d]{2})([A\d])$/;
    export default {
        name: "TabDatos",
        props: ['id'],
        data(){
            return{
                rfc : '',
                razon : '',
                nss : '',
                telefono : '',
                contacto : '',
                correo : '',
                giro:'',
                especialidad : '',
                rfcValidate: false,
            }
        },
        methods: {
            invalidRFC() {
                this.rfcValidate = true;
            },
            validate() {
                this.invalidRFC();
                this.$validator.validate().then(result => {
                    if (result) {
                        //this.update()
                    }
                });
            },
        }
    }
</script>

<style scoped>

</style>
