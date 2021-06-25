<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-primary" title="Editar">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-pencil"></i> Editar Límites de Remesa Extraordinaria</h5>
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
                            <div class="row" v-if="folio">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Proyecto:</label>
                                        <div >
                                            {{folio.proyecto}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                     <div class="form-group">
                                         <div style="text-align: center">
                                            <label >Año:</label>
                                         </div>
                                        <div style="text-align: center">
                                            {{folio.anio}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                     <div class="form-group">
                                         <div style="text-align: center">
                                              <label >Semana:</label>
                                         </div>
                                         <div style="text-align: center">
                                            {{folio.numero_semana}}
                                         </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="folio">
                                <div class="col-md-12">
                                   <div class="form-group row">
                                       <label class="col-md-3 col-form-label" for="CantidadExtraordinariasPermitidas">Cantidad Límite:</label>
                                       <div class="col-md-3">
                                           <input type="number"
                                                  class="form-control"
                                                  id="CantidadExtraordinariasPermitidas"
                                                  name="CantidadExtraordinariasPermitidas"
                                                  data-vv-as="Cantidad Límite de Remesas extraordinarias"
                                                  v-validate="{required: true}"
                                                  :class="{'is-invalid': errors.has('CantidadExtraordinariasPermitidas')}"
                                                  v-model="folio.cantidad_limite"
                                                  style="text-align: right"
                                           />
                                           <div class="invalid-feedback" v-show="errors.has('CantidadExtraordinariasPermitidas')">{{ errors.first('CantidadExtraordinariasPermitidas') }}</div>
                                       </div>
                                       <label class="col-md-3 col-form-label" for="MontoLimiteExtraordinarias" >Monto Límite:</label>
                                       <div class="col-md-3">
                                            <input id="MontoLimiteExtraordinarias"
                                                   name="MontoLimiteExtraordinarias"
                                                   data-vv-as="Monto Limite"
                                                   :class="{'is-invalid': errors.has('MontoLimiteExtraordinarias')}"
                                                   v-validate="{ required: true, numeric:true  }"
                                                   class="form-control"
                                                   v-model="folio.monto_limite"
                                                   style="text-align: right"
                                            />
                                            <div class="invalid-feedback" v-show="errors.has('MontoLimiteExtraordinarias')">{{ errors.first('MontoLimiteExtraordinarias') }}</div>
                                        </div>
                                   </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir">
                            <i class="fa fa-times"></i>Cerrar
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
        name: "limite-remesa-edit",
        props: ['anio','semana','id_proyecto'],
        data() {
            return {
                cargando : true,
                folio : null
            }
        },
        methods: {
            salir() {
                $(this.$refs.modal).modal('hide');
            },
            find() {
                this.folio = null;
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                return this.$store.dispatch('remesas/remesa-folio/find', {
                    params: {
                        id_proyecto: this.id_proyecto,
                        anio: this.anio,
                        numero_semana: this.semana
                    }
                }).then(data => {
                    this.folio = data
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
                return this.$store.dispatch('remesas/remesa-folio/update', {
                    data: this.folio
                })
                    .then((data) => {
                        this.salir()
                        return this.$store.dispatch('remesas/remesa-folio/paginate', { params: {scope:'ordenarPorAnioSemana'}})
                            .then(data => {
                                this.$store.commit('remesas/remesa-folio/SET_FOLIOS', data.data);
                                this.$store.commit('remesas/remesa-folio/SET_META', data.meta);
                            })
                    })
            },
        }
    }
</script>

<style scoped>

</style>
