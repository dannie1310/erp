<template>
    <span>
        <button v-if="$root.can('ingresar_folio_contpaq') && (poliza.estatus == -1 || poliza.estatus == 0)" class="btn btn-app btn-info pull-right" @click="openModal">
            <i class="fa fa-i-cursor"></i> Ingrear Folio Contpaq
        </button>

         <!-- Modal Folio Contpaq-->
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">INGRESAR FOLIO CONTPAQ</h5>
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

        mounted() {
            this.init();
        },

        methods: {
            openModal() {
                $(this.$refs.modal).modal('show');
            },

            init() {
                this.fecha = this.poliza.fecha;
                this.poliza_contpaq = this.poliza.poliza_contpaq;
                this.$validator.reset()
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.save({
                            id: this.poliza.id,
                            data: this.$data,
                            params: { include: 'transaccionAntecedente,movimientos,traspaso' }
                        })
                            .then(data => {
                                this.$store.commit('contabilidad/poliza/UPDATE_POLIZA', data);
                                $(this.$refs.modal).modal('hide');
                            })
                    }
                });
            },

            save(payload) {
                return this.$store.dispatch('contabilidad/poliza/update', payload);
            }
        }
    }
</script>