<template>
   <span>
      <button @click="find" type="button" class="btn btn-sm btn-outline-info" title="Editar">
         <i class="fa fa-pencil"></i>
      </button>
      <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-pencil"></i> EDITAR OPERADOR</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <form role="form" @submit.prevent="validate">
                  <div class="modal-body">
                     <div v-if="cargando">
                        <div class="row" >
                           <div class="col-md-12">
                                 <div class="spinner-border text-success" role="status">
                                    <span class="sr-only">Cargando...</span>
                                 </div>
                           </div>
                        </div>
                     </div>
                     <div v-else>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group col-md-12 error-content">
                                 <label for="Nombre" class="col-form-label">Nombre:</label>
                                 <input
                                          type="text"
                                          name="Nombre"
                                          data-vv-as="'Nombre'"
                                          v-validate="{required: true, max:50}"
                                          class="form-control"
                                          id="Nombre"
                                          v-model="operador.nombre"
                                          :class="{'is-invalid': errors.has('Nombre')}" />
                                 <div class="invalid-feedback" v-show="errors.has('Nombre')">{{ errors.first('Nombre') }}</div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group col-md-12 error-content">
                                 <label for="Direccion" class="col-form-label">Dirección:</label>
                                 <input
                                          type="text"
                                          name="Direccion"
                                          data-vv-as="'Dirección'"
                                          v-validate="{required: true, max:100}"
                                          class="form-control"
                                          id="Direccion"
                                          v-model="operador.direccion"
                                          :class="{'is-invalid': errors.has('Direccion')}" />
                                 <div class="invalid-feedback" v-show="errors.has('Direccion')">{{ errors.first('Direccion') }}</div>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                               <div class="form-group col-md-12 error-content">
                                 <label for="NoLicencia" class="col-form-label">Número de Licencia:</label>
                                 <input
                                          type="text"
                                          name="NoLicencia"
                                          data-vv-as="'Número de Licencia'"
                                          v-validate="{required: true, max:30}"
                                          class="form-control"
                                          id="NoLicencia"
                                          v-model="operador.no_licencia"
                                          :class="{'is-invalid': errors.has('NoLicencia')}" />
                                 <div class="invalid-feedback" v-show="errors.has('NoLicencia')">{{ errors.first('NoLicencia') }}</div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group col-md-12 error-content">
                                 <label for="VigenciaLicencia" class="col-form-label">Vigencia de Licencia:</label>
                                 <datepicker v-model = "operador.vigencia_licencia"
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
                  </div>
                  <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i>Cerrar</button>
                        <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate" v-if="!cargando"><i class="fa fa-save"></i> Guardar</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </span>
</template>

<script>
   import Datepicker from 'vuejs-datepicker';
   import {es} from 'vuejs-datepicker/dist/locale';
   export default {
      name: "operador-edit",
      props: ['id'],
      components: {Datepicker, es},
      data() {
         return {
            es:es,
            cargando: false,
            operador : {},
         }
      },
      methods: {
         find() {
            this.cargando = true;
            this.operador= {};
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
            return this.$store.dispatch('acarreos/operador/find', {
               id: this.id,
               params: {}
            }).then(data => {
               this.operador = data;
               this.cargando = false;
            })
         },
         formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
         },
         salir() {
               $(this.$refs.modal).modal('hide');
         },
         validate() {
               this.$validator.validate().then(result => {
                  if (result) {
                     this.update()
                  }
               });
         },
         update() {
               var datos = {
                  'Nombre' : this.operador.nombre,
                  'Direccion' : this.operador.direccion,
                  'NoLicencia' : this.operador.no_licencia,
                  'VigenciaLicencia' : this.operador.vigencia_licencia,
               }
               return this.$store.dispatch('acarreos/operador/update', {
                  id: this.id,
                  data: datos
               })
               .then((data) => {
                  this.$store.commit('acarreos/operador/UPDATE_OPERADOR', data);
                  this.salir()
               })
         },
      },
      computed: {
      }
   }
</script>

<style scoped>

</style>
