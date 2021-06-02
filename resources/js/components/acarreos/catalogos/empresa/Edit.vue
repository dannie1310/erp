<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"></i> EDITAR EMPRESA</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
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
                            <div class="row" v-if="empresa">
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
                                                  v-model="empresa.razon_social" />
                                           <div class="invalid-feedback" v-show="errors.has('razon_social')">{{ errors.first('razon_social') }}</div>
                                       </div>
                                   </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">RFC:</label>
                                        <div class="col-md-4">
                                            <input id="rfc"
                                                   name="rfc"
                                                   data-vv-as="RFC"
                                                   :class="{'is-invalid': errors.has('rfc')}"
                                                   v-validate="{ required: true, regex: /^([A-ZÑ&]{3,4})(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]))([A-Z\d]{2})([A\d])$/ }"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="empresa.rfc"
                                                   :maxlength="13" />
                                            <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
                                        </div>
                                        <label class="col-md-2 col-form-label">Estatus:</label>
                                        <div class="col-md-4">
                                            <select class="form-control"
                                                    name="estado"
                                                    data-vv-as="Estado"
                                                    v-model="empresa.estado"
                                                    v-validate="{required: true}"
                                                    :class="{'is-invalid': errors.has('estado')}"
                                                    id="estado">
                                                    <option value>-- Seleccionar--</option>
                                                    <option value="1" >ACTIVO</option>
                                                    <option value="0" >INACTIVO</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has('estado')">{{ errors.first('estado') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir">
                            <i class="fa fa-angle-left"></i>Regresar
                        </button>
                        <button @click="validate" type="button" class="btn btn-primary" :disabled="errors.count() > 0 || cargando == true">
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
        name: "empresa-edit",
        props: ['id'],
        data() {
            return {
                cargando : true,
                empresa : ''
            }
        },
        methods: {
            salir() {
                $(this.$refs.modal).modal('hide');
            },
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                return this.$store.dispatch('acarreos/empresa/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.empresa = data
                }).finally(()=>
                {
                    this.cargando = false;
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
                return this.$store.dispatch('acarreos/empresa/update', {
                    id: this.id,
                    data: this.empresa
                })
                    .then((data) => {
                        this.$store.commit('acarreos/empresa/UPDATE_EMPRESA', data);
                        this.salir()
                    })
            },
        }
    }
</script>

<style scoped>

</style>
