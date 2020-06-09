<template>
  <span>
      <button @click="find()" type="button" class="btn btn-sm btn-outline-danger" :disabled="cargando" title="Eliminar Pago">
          <i class="fa fa-trash"></i>
      </button>
      <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-trash"></i> PAGO</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <form role="form" @submit.prevent="validate">
                      <div class="modal-body" v-if="pago">
                          <div class="row">
                               <div class="col-12">
                                   <div class="invoice p-3 mb-3">
                                       <div class="row col-md-12">
                                           <div class="col-md-6">
                                               <h5>Folio: &nbsp; <b>{{pago.numero_folio_format}}</b></h5>
                                           </div>
                                           <div class="col-md-6">
                                               <h5>Fecha: {{pago.fecha_format}}</h5>
                                           </div>
                                       </div>
                                       <div class="table-responsive col-md-12">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Beneficiario:</b></td>
                                                        <td class="bg-gray-light">{{pago.empresa ? pago.empresa.razon_social : ''}}</td>
                                                        <td class="bg-gray-light"><b>Cuenta:</b></td>
                                                        <td class="bg-gray-light">{{pago.cuenta.numero}} ( {{pago.cuenta.abreviatura}} )</td>
                                                        <td class="bg-gray-light"><b>Estado:</b></td>
                                                        <td class="bg-gray-light">{{pago.estado_string}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Concepto:</b></td>
                                                        <td class="bg-gray-light" colspan="3">{{pago.observaciones.toLocaleUpperCase()}}</td>
                                                        <td class="bg-gray-light"><b>Usuario Registró:</b></td>
                                                        <td class="bg-gray-light">{{(pago.usuario) ? pago.usuario.nombre : '------------'}}</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                   </div>
                               </div>
                              <div class="invoice p-3 mb-3" style="width:100%">
                                  <div class="row">
                                      <div class="col-md-12">
                                          <label for="motivo">Motivo de eliminación:</label>
                                      </div>
                                  </div>
                                  <div class="row">
                                      <div class="col-md-12">
                                          <div class="form-group row error-content">
                                              <div class="col-md-12">
                                                  <textarea
                                                    name="motivo"
                                                    id="motivo"
                                                    class="form-control"
                                                    v-model="motivo"
                                                    v-validate="{required: true}"
                                                    data-vv-as="Motivo"
                                                    :class="{'is-invalid': errors.has('motivo')}"
                                                  ></textarea>
                                                  <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                          <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0 || motivo == ''">Eliminar</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </span>
</template>

<script>
    export default {
        name: "pago-delete",
        props: ['id'],
        data(){
            return{
                cargando : false,
                motivo : ''
            }
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('finanzas/pago/SET_PAGO', null);
                return this.$store.dispatch('finanzas/pago/find', {
                    id: this.id,
                    params:{include: ['moneda','cuenta','empresa', 'usuario']}
                })
                    .then(data => {
                        this.$store.commit('finanzas/pago/SET_PAGO', data);
                        $(this.$refs.modal).appendTo('body')
                        $(this.$refs.modal).modal('show')
                        this.cargando = false;
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('finanzas/pago/eliminar', {
                    id: this.id,
                    params: {data: [this.$data.motivo]}
                })
                    .then(data => {
                        this.$store.commit('finanzas/pago/DELETE_PAGO', {id: this.id})
                        $(this.$refs.modal).modal('hide');
                        this.$store.dispatch('finanzas/pago/paginate', {
                            params: {
                                include: ['moneda','cuenta','empresa'],  sort: 'numero_folio', order: 'desc'
                            }
                        }).then(data => {
                            this.$store.commit('finanzas/pago/SET_PAGOS', data.data);
                            this.$store.commit('finanzas/pago/SET_META', data.meta);
                        })
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if (this.motivo == '') {
                            swal('¡Error!', 'Debe colocar un motivo para realizar la operación.', 'error')
                        } else {
                            this.eliminar()
                        }
                    }
                });
            },
        },
        computed: {
            pago() {
                return this.$store.getters['finanzas/pago/currentPago']
            }
        }
    }
</script>

<style scoped>

</style>
