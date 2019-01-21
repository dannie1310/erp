<template>
    <span>
        <button v-if="$root.can('ingresar_folio_contpaq') && (poliza.estatus == -1 || poliza.estatus == 0)" class="btn btn-app btn-info pull-right" data-toggle="modal" :data-target="`#folioContpaqModal${poliza.id}`">
            <i class="fa fa-i-cursor"></i> Ingrear Folio Contpaq
        </button>

         <!-- Modal Folio Contpaq-->
        <div class="modal fade" :id="`folioContpaqModal${poliza.id}`" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">INGRESAR FOLIO CONTPAQ</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                           <div class="row">
                               <div class="col-md-6">
                                   <div class="form-group">
                                       <label for="folio_contpaq"><strong>Número de Folio</strong></label>
                                       <input type="number"
                                              id="folio_contpaq"
                                              name="poliza_contpaq"
                                              class="form-control"
                                              v-model="poliza_contpaq"
                                              v-validate="{required: true}"
                                              data-vv-as="Número de Folio"
                                              :class="{'is-invalid': errors.has('poliza_contpaq')}"
                                       >
                                       <div class="invalid-feedback" v-show="errors.has('poliza_contpaq')">
                                           {{ errors.first('poliza_contpaq') }}
                                       </div>
                                   </div>
                               </div>
                               <div class="col-md-6">
                                   <div class="form-group">
                                       <label for="fecha"><strong>Fecha de Prepóliza</strong></label>
                                       <input type="date"
                                              id="fecha"
                                              class="form-control"
                                              name="fecha"
                                              v-model="fecha"
                                              v-validate="{required: true, date_format: 'YYYY-MM-DD'}"
                                              data-vv-as="Fecha de Prepóliza"
                                              :class="{'is-invalid': errors.has('fecha')}"
                                       />
                                       <div class="invalid-feedback" v-show="errors.has('fecha')">
                                           {{ errors.first('fecha') }}
                                       </div>
                                   </div>
                               </div>
                           </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click="init">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "poliza-ingresar-folio",
        props: ['poliza'],
        data() {
            return {
                fecha: '',
                poliza_contpaq: '',
                estatus: 3
            }
        },

        methods: {
            init() {
                this.fecha = '';
                this.poliza_contpaq = '';

                this.$validator.reset()
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.save()
                    }
                });
            },

            save() {
                let self = this
                $(`#folioContpaqModal${self.poliza.id}`).modal('hide');
                Swal({
                    title: "Ingresar Folio",
                    text: "¿Estás seguro de que la información es correcta?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Si, Continuar",
                    cancelButtonText: "No, Cancelar",
                }).then(function (result) {
                    if(result.value) {
                        self.$store.dispatch('contabilidad/poliza/update', {
                            id: self.poliza.id,
                            data: self.$data,
                            params: {include: 'transaccionAntecedente,movimientos,traspaso'}
                        })
                            .then(() => {
                                Swal({
                                    type: "success",
                                    title: '¡Correcto!',
                                    text: 'Folio Contpaq ingresado correctamente',
                                    confirmButtonText: "Ok",
                                    closeOnConfirm: false
                                }).then(function () {
                                    self.init();
                                });
                            })
                    }
                });

            }
        }
    }
</script>