<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-primary" title="Editar">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-pencil"></i> Editar </h5>
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
                            <div class="row" v-if="proyecto">
                                <div class="col-md-12" style="text-align: center">
                                    <div class="form-group">
                                        <h6>{{proyecto.nombre}}</h6>
                                    </div>
                                </div>

                            </div>

                            <div class="row" v-if="proyecto">
                                <div class="col-md-12">
                                   <div class="form-group row">
                                       <label class="col-md-6 col-form-label" for="CantidadExtraordinariasPermitidas">Cantidad Límite:</label>
                                       <div class="col-md-6">
                                           <input type="number"
                                                  class="form-control"
                                                  id="CantidadExtraordinariasPermitidas"
                                                  name="CantidadExtraordinariasPermitidas"
                                                  data-vv-as="Cantidad Límite de Remesas extraordinarias"
                                                  v-validate="{required: true}"
                                                  :class="{'is-invalid': errors.has('CantidadExtraordinariasPermitidas')}"
                                                  v-model="cantidad_limite"
                                                  style="text-align: right"
                                           />
                                           <div class="invalid-feedback" v-show="errors.has('CantidadExtraordinariasPermitidas')">{{ errors.first('CantidadExtraordinariasPermitidas') }}</div>
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
        name: "limite-remesa-proyecto-edit",
        props: ['id_proyecto'],
        data() {
            return {
                cargando : true,
                cantidad_limite : null,
                proyecto : null,
            }
        },
        methods: {
            salir() {
                $(this.$refs.modal).modal('hide');
            },
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                return this.$store.dispatch('remesas/proyecto/find', {
                    id: this.id_proyecto,
                }).then(data => {
                    this.proyecto = data;
                    this.cantidad_limite = data.cantidad_limite_extraordinarias;
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
                return this.$store.dispatch('remesas/proyecto/update', {
                    id: this.id_proyecto,
                    data: {CantidadExtraordinariasPermitidas:this.cantidad_limite}
                })
                    .then((data) => {
                        this.$store.commit('remesas/proyecto/UPDATE_PROYECTO', data);
                        this.salir();
                    })
            },
        }
    }
</script>

<style scoped>

</style>
