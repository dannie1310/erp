<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"></i> EDITAR CONFIGURACIÓN LIMITE REMESA EXTRAORDINARIA</h5>
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
                                <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Proyecto:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h6>{{folio.proyecto}}</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Año:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h6>{{folio.anio}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Número Semana:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h6>{{folio.numero_semana}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <div class="col-md-12">
                                   <div class="form-group row">
                                       <label class="col-md-2 col-form-label">Remesas Extraordinarias Máximas:</label>
                                       <div class="col-md-10">
                                           <input type="number"
                                                  class="form-control"
                                                  id="CantidadExtraordinariasPermitidas"
                                                  name="CantidadExtraordinariasPermitidas"
                                                  data-vv-as="REMESAS EXTRAORDINARIAS MÁXIMAS"
                                                  v-validate="{required: true}"
                                                  :class="{'is-invalid': errors.has('CantidadExtraordinariasPermitidas')}"
                                                  v-model="folio.cantidad_limite" />
                                           <div class="invalid-feedback" v-show="errors.has('CantidadExtraordinariasPermitidas')">{{ errors.first('CantidadExtraordinariasPermitidas') }}</div>
                                       </div>
                                   </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Monto Límite:</label>
                                        <div class="col-md-4">
                                            <input type="number"
                                                   id="MontoLimiteExtraordinarias"
                                                   name="MontoLimiteExtraordinarias"
                                                   data-vv-as="Monto Limite"
                                                   :class="{'is-invalid': errors.has('MontoLimiteExtraordinarias')}"
                                                   v-validate="{ required: true }"
                                                   class="form-control"
                                                   v-model="folio.monto_limite"
                                                   :maxlength="13" />
                                            <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
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
        name: "limite-remesa-edit",
        props: ['anio','semana','id_proyecto'],
        data() {
            return {
                cargando : true,
                folio : ''
            }
        },
        methods: {
            salir() {
                $(this.$refs.modal).modal('hide');
            },
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                return this.$store.dispatch('remesas/remesa-folio/find', {
                    id: this.id_proyecto,
                    params: {
                        anio: this.anio,
                        semana: this.semana
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
                    id: this.id,
                    data: this.folio
                })
                    .then((data) => {
                        this.$store.commit('remesas/remesa-folio/UPDATE_FOLIO', data);
                        this.salir()
                    })
            },
        }
    }
</script>

<style scoped>

</style>
