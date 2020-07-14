<template>
    <span>
        <button @click="find()" v-if="$root.can('consultar_sucursal_banco')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver Sucursal">
            <i class="fa fa-eye"></i>
        </button>
          <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CONSULTA DE SUCURSAL BANCARIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <div class="row">
                                <!---Sucursal-->
                                   <div class="col-md-12" v-if="sucursal">
                                                 <div class="form-group">
                                                        <label><b>Descripción: </b></label>
                                                     {{ sucursal.descripcion }}
                                                    </div>
                                           </div>
                                <!--Ubicación -->
                                <div class="col-md-12  mt-3 text-left" >
                                      <label class="text-secondary">Ubicación </label>
                                       <hr style="color: #0056b2; margin-top:auto;" width="95%" size="10" />
                                </div>
                                <!--Dirección-->
                                  <div class="col-md-12" v-if="sucursal">
                                                 <div class="form-group">
                                                        <label><b>Dirección: </b></label>
                                                     {{ sucursal.direccion }}
                                                    </div>
                                           </div>
                                <!--Ciudad-->
                                     <div class="col-md-12" v-if="sucursal">
                                                 <div class="form-group">
                                                        <label><b>Ciudad: </b></label>
                                                     {{ sucursal.ciudad }}
                                                    </div>
                                           </div>
                                <!--Código Postal-->

                                 <div class="col-md-6" v-if="sucursal">
                                                 <div class="form-group">
                                                        <label><b>CP: </b></label>
                                                     {{ sucursal.codigo_postal }}
                                                    </div>
                                           </div>
                                <!--Estado-->
                                     <div class="col-md-6" v-if="sucursal">
                                                 <div class="form-group">
                                                        <label><b>Estado: </b></label>
                                                     {{ sucursal.estado }}
                                                    </div>
                                           </div>

                                <!--Teléfonos -->
                                <div class="col-md-12 mt-5 text-left" >
                                      <label class="text-secondary">Teléfonos</label>
                                       <hr style="color: #0056b2; margin-top:auto;" width="95%" size="10" />
                                </div>

                                <!--Voz-->
                              <div class="col-md-6" v-if="sucursal">
                                                 <div class="form-group">
                                                        <label><b>Voz: </b></label>
                                                     {{ sucursal.telefono }}
                                                    </div>
                                           </div>
                                <!--Fax-->
                                  <div class="col-md-6" v-if="sucursal">
                                                 <div class="form-group">
                                                        <label><b>Fax: </b></label>
                                                     {{ sucursal.fax }}
                                                    </div>
                                           </div>
                                <!--Contacto-->
                                    <div class="col-md-6" v-if="sucursal">
                                                 <div class="form-group">
                                                        <label><b>Contacto: </b></label>
                                                     {{ sucursal.contacto}}
                                                    </div>
                                           </div>
                                <!--Check Central-->
                                   <div class="col-md-6" v-if="sucursal">
                                                 <div class="form-group" v-if="sucursal.casa_central === 'S'">
                                                        <label><b>Central: </b></label>
                                                                 Sí
                                                    </div>
                                       <div class="form-group" v-if="sucursal.casa_central === 'N'">
                                                        <label><b>Central: </b></label>
                                                                No
                                                    </div>
                                           </div>



                                </div>
                            </div>

                        </div>
                </div>
            </div>
    </span>
</template>

<script>
    export default {
        name: "sucursal-show",
        props: ['id'],
        data(){
            return{
                sucursales: null,
                cargando: false,
            }
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('cadeco/sucursal/SET_SUCURSAL', null);
                return this.$store.dispatch('cadeco/sucursal/find', {
                    id: this.id
                }).then(data => {
                    this.$store.commit('cadeco/sucursal/SET_SUCURSAL', data);
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                })
            }
        },
        computed: {
            sucursal() {
                return this.$store.getters['cadeco/sucursal/currentSucursal']
            }
        }
    }
</script>

<style scoped>

</style>
