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
                                    v-model="empresa.razon_social"
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
                                   v-model="empresa.rfc"
                                   :class="{'is-invalid': errors.has('rfc')}"
                                   v-validate="{ required: true, regex: /^([A-ZÑ&]{3,4})(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]))([A-Z\d]{2})([A\d])$/ }"
                                   id="rfc"/>
                            <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="nss" class="col-form-label">NSS:</label>
                            <input class="form-control"
                                   name="nss"
                                   data-vv-as="NSS"
                                   v-model="empresa.nss"
                                   v-validate="{ required: true,numeric:true, digits:11}"
                                   id="nss"
                                   :class="{'is-invalid': errors.has('nss')}"
                                   placeholder="NSS"/>
                            <div class="invalid-feedback" v-show="errors.has('nss')">{{ errors.first('nss') }}</div>
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
    export default {
        name: "TabDatosPrestadora",
        props: ['prestadora'],
        data(){
            return{
                empresa: [],
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
                    this.empresa = data;
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },
            update() {
                this.empresa.rfc_prestadora = this.prestadora.rfc
                this.empresa.razon_social = this.empresa.razon_social.toUpperCase();
                this.empresa.rfc = this.empresa.rfc.toUpperCase();
                return this.$store.dispatch('padronProveedores/empresa/update', {
                    id: this.prestadora.id,
                    data: this.$data.empresa
                }).then((data) => {

                })
            },
        }
    }
</script>

<style scoped>

</style>
