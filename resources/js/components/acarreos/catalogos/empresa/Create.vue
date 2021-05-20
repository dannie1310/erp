<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_empresa')" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"></i> REGISTRAR EMPRESA</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label">Razón Social:</label>
                                    <div class="col-md-10">
                                        <input type="text"
                                               class="form-control"
                                               id="razon_social"
                                               name="razon_social"
                                               data-vv-as="Razón Social"
                                               v-validate="{required: true, min:6}"
                                               :class="{'is-invalid': errors.has('razon_social')}"
                                               v-model="razon_social" />
                                        <div class="invalid-feedback" v-show="errors.has('razon_social')">{{ errors.first('razon_social') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">RFC:</label>
                                        <div class="col-md-10">
                                            <input id="rfc"
                                                   name="rfc"
                                                   data-vv-as="RFC"
                                                   :class="{'is-invalid': errors.has('rfc')}"
                                                   v-validate="{ required: true, regex: /^([A-ZÑ&]{3,4})(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]))([A-Z\d]{2})([A\d])$/ }"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="rfc"
                                                   :maxlength="13" />
                                            <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir">
                            <i class="fa fa-angle-left"></i>Regresar
                        </button>
                        <button @click="validate" type="button" class="btn btn-primary" :disabled="errors.count() > 0">
                          <i class="fa fa-save"></i> Guardar
                      </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "Create",
        data() {
            return {
                razon_social : '',
                rfc : ''
            }
        },
        methods : {
            init() {
                this.razon_social = '';
                this.rfc = '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            salir() {
                $(this.$refs.modal).modal('hide');
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store();
                    }
                });
            },
            store() {
                return this.$store.dispatch('acarreos/empresa/store', {
                    razonSocial: this.razon_social,
                    RFC: this.rfc
                }).then((data) => {
                    this.salir()
                    this.$emit('created', data)
                });
            },
        }
    }
</script>

<style scoped>

</style>
