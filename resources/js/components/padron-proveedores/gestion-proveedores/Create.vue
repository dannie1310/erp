<template>
    <span>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group row error-content">
                    <label for="razon_social" class="col-md-3 col-form-label">Razón Social:</label>
                    <div class="col-md-9">
                        <textarea
                                style="text-transform:uppercase;"
                                type="text"
                                name="razon_social"
                                data-vv-as="'Razón Social'"
                                v-validate="{required: true, min:6}"
                                class="form-control"
                                id="razon_social"
                                placeholder="Razón Social"
                                v-model="registro_proveedor.razon_social"
                                :class="{'is-invalid': errors.has('razon_social')}"></textarea>
                        <div class="invalid-feedback" v-show="errors.has('razon_social')">{{ errors.first('razon_social') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>RFC: </b> </label>
                    <div class="col-md-9">
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="rfc"
                               data-vv-as="'RFC'"
                               v-model="registro_proveedor.rfc"
                               :class="{'is-invalid': errors.has('rfc')}"
                               v-validate="{ required: true, regex: /^([A-ZÑ&]{3,4})(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]))([A-Z\d]{2})([A\d])$/ }"
                               id="rfc"
                               placeholder="RFC sin espacios ni guiones" :maxlength="13"/>
                        <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>NSS: </b> </label>
                    <div class="col-md-9">
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="numero_imss"
                               data-vv-as="'NSS'"
                               v-model="registro_proveedor.numero_imss"
                               :class="{'is-invalid': errors.has('numero_imss')}"
                               v-validate="{ required: true, numeric:true, min:11 }"
                               id="numero_imss"
                               placeholder="Número del Seguro Social" :maxlength="11"/>
                        <div class="invalid-feedback" v-show="errors.has('numero_imss')">{{ errors.first('numero_imss') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>Contacto: </b> </label>
                    <div class="col-md-9">
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="contacto"
                               data-vv-as="'Contacto'"
                               v-model="registro_proveedor.contacto"
                               :class="{'is-invalid': errors.has('contacto')}"
                               v-validate="{ required: true, min:10 }"
                               id="contacto"
                               placeholder="Nombre del Contacto" :maxlength="250"/>
                        <div class="invalid-feedback" v-show="errors.has('contacto')">{{ errors.first('contacto') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>Teléfono: </b> </label>
                    <div class="col-md-9">
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="telefono"
                               data-vv-as="'Teléfono'"
                               v-model="registro_proveedor.telefono"
                               :class="{'is-invalid': errors.has('telefono')}"
                               v-validate="{ required: true, numeric:true }"
                               id="telefono"
                               placeholder="Teléfono de Contacto" :maxlength="20"/>
                        <div class="invalid-feedback" v-show="errors.has('telefono')">{{ errors.first('telefono') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>Correo: </b> </label>
                    <div class="col-md-9">
                        <input class="form-control"
                               name="correo"
                               data-vv-as="'Correo'"
                               v-model="registro_proveedor.correo"
                               :class="{'is-invalid': errors.has('correo')}"
                               v-validate="{ required: true, email:true }"
                               id="correo"
                               placeholder="CORREO DE CONTACTO" :maxlength="50"/>
                        <div class="invalid-feedback" v-show="errors.has('correo')">{{ errors.first('correo') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>Giro: </b> </label>
                    <div class="col-md-9">
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="giro"
                               data-vv-as="'Giro'"
                               v-model="registro_proveedor.giro"
                               :class="{'is-invalid': errors.has('giro')}"
                               v-validate="{ required: true }"
                               id="giro"
                               placeholder="Giro" :maxlength="50"/>
                        <div class="invalid-feedback" v-show="errors.has('giro')">{{ errors.first('giro') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>Especialidad: </b> </label>
                    <div class="col-md-9">
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="especialidad"
                               data-vv-as="'Especialidad'"
                               v-model="registro_proveedor.especialidad"
                               :class="{'is-invalid': errors.has('especialidad')}"
                               v-validate="{ required: true }"
                               id="especialidad"
                               placeholder="Especialidad" :maxlength="50"/>
                        <div class="invalid-feedback" v-show="errors.has('especialidad')">{{ errors.first('especialidad') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button type="button" class="btn btn-primary pull-right" :disabled="errors.count() > 0 || cargando" v-on:click="validate">
                    <span v-if="cargando">
                        <i class="fa fa-spin fa-spinner"></i>
                    </span>
                    <span v-else>
                        <i class="fa fa-save"></i> Guardar
                    </span>
                </button>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "Create",
        data() {
            return {
                cargando: false,
                registro_proveedor : {
                    razon_social : '',
                    rfc : '',
                    numero_imss: '',
                    contacto : '',
                    telefono : '',
                    correo : '',
                    giro : '',
                    especialidad: '',
                },
            }
        },
        methods:{

            store() {
                return this.$store.dispatch('padronProveedores/empresa/store', this.$data.registro_proveedor)
                    .then(data => {

                    }).finally( ()=>{
                        this.cargando = false;
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    this.registro_proveedor.razon_social = this.registro_proveedor.razon_social.toUpperCase();
                    this.registro_proveedor.rfc = this.registro_proveedor.rfc.toUpperCase();
                    if (result) {
                        this.store()
                    }
                });
            },
        },
    }
</script>

<style scoped>

</style>