<template>
    <span>
        <button @click="init" v-if="$root.can('registrar_operador')||true" class="btn btn-app btn-info pull-right">
            <i class="fa fa-plus"></i> Registrar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"></i> REGISTRAR OPERADOR</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group col-md-12 error-content">
                                 <label for="Nombre" class="col-form-label">Nombre:</label>
                                 <input style="text-transform:uppercase;"
                                          type="text"
                                          name="Nombre"
                                          data-vv-as="'Nombre'"
                                          v-validate="{required: true, max:50}"
                                          class="form-control"
                                          id="Nombre"
                                          v-model="Nombre"
                                          :class="{'is-invalid': errors.has('Nombre')}" />
                                 <div class="invalid-feedback" v-show="errors.has('Nombre')">{{ errors.first('Nombre') }}</div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group col-md-12 error-content">
                                 <label for="Direccion" class="col-form-label">Dirección:</label>
                                 <input style="text-transform:uppercase;"
                                          type="text"
                                          name="Direccion"
                                          data-vv-as="'Dirección'"
                                          v-validate="{required: true, max:100}"
                                          class="form-control"
                                          id="Direccion"
                                          v-model="Direccion"
                                          :class="{'is-invalid': errors.has('Direccion')}" />
                                 <div class="invalid-feedback" v-show="errors.has('Direccion')">{{ errors.first('Direccion') }}</div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                               <div class="form-group col-md-12 error-content">
                                 <label for="NoLicencia" class="col-form-label">Número de Licencia:</label>
                                 <input style="text-transform:uppercase;"
                                          type="text"
                                          name="NoLicencia"
                                          data-vv-as="'Número de Licencia'"
                                          v-validate="{required: true, max:30}"
                                          class="form-control"
                                          id="NoLicencia"
                                          v-model="NoLicencia"
                                          :class="{'is-invalid': errors.has('NoLicencia')}" />
                                 <div class="invalid-feedback" v-show="errors.has('NoLicencia')">{{ errors.first('NoLicencia') }}</div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group col-md-12 error-content">
                                 <label for="VigenciaLicencia" class="col-form-label">Vigencia de Licencia:</label>
                                 <datepicker v-model = "VigenciaLicencia"
                                             name = "VigenciaLicencia"
                                             :format = "formatoFecha"
                                             :language = "es"
                                             :bootstrap-styling = "true"
                                             class = "form-control"
                                             v-validate="{required: true}"
                                             :class="{'is-invalid': errors.has('VigenciaLicencia')}"
                                 />
                                 <div class="invalid-feedback" v-show="errors.has('VigenciaLicencia')">{{ errors.first('VigenciaLicencia') }}</div>
                              </div>
                           </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i>Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                </div>
            </div>
        </div>
      </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "operador-create",
        props: ['id'],
        components: {Datepicker, es},
        data() {
            return {
                es:es,
                Nombre:'',
                Direccion:'',
                NoLicencia:'',
                VigenciaLicencia:'',
                cargando : true
            }
        },
        methods: {
           formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
           init() {
                this.Nombre = '';
                this.Direccion = '';
                this.NoLicencia = '';
                this.VigenciaLicencia = '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            store() {
               return this.$store.dispatch('acarreos/operador/store', {
                  Nombre: this.Nombre.toUpperCase(),
                  Direccion: this.Direccion.toUpperCase(),
                  NoLicencia: this.NoLicencia.toUpperCase(),
                  VigenciaLicencia: this.VigenciaLicencia
               })
               .then((data) => {
                  $(this.$refs.modal).modal('hide');
                  this.$emit('created', data)
               });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store();
                    }
                });
            },
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
