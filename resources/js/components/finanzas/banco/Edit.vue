<template>
    <span>
    <nav>

                                          <nav>
                      <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <!--    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Identificación</a>-->
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Cuentas</a>
                        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Sucursales</a>
                      </div>
                    </nav>

                <div class="tab-content" id="nav-tabContent">

                <!--  <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">...</div>-->
                  <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">Cuentas</div>
                  <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" style="display:block;">
                      <index-sucursal></index-sucursal>
                  </div>
                </div>
    </nav>
    </span>

</template>
<!--        		   <button  @click="find()"  type="button" class="btn btn-sm btn-outline-info">-->
<!--            <i class="fa fa-pencil"></i>-->
<!--        </button>-->

<!--        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">-->
<!--            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">-->
<!--                <div class="modal-content">-->
<!--                    <div class="modal-header">-->
<!--                        <h5 class="modal-title" id="exampleModalLongTitle">EDICIÓN DE BANCOS</h5>-->
<!--                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
<!--                            <span aria-hidden="true">&times;</span>-->
<!--                        </button>-->
<!--                    </div>-->

<!--                        <div class="modal-body">-->
<!--                            <div class="row">-->

<!--                                 <div class="col-md-12"  v-if="banco">-->
<!--                                          <div class="form-group">-->
<!--                                                    <label><b>Banco:</b></label>-->
<!--                                                    {{ banco.razon_social }}-->
<!--                                                </div>-->
<!--                                  <nav>-->
<!--              <div class="nav nav-tabs" id="nav-tab" role="tablist">-->
<!--            &lt;!&ndash;    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Identificación</a>&ndash;&gt;-->
<!--                <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Cuentas</a>-->
<!--                <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Sucursales</a>-->
<!--              </div>-->
<!--            </nav>-->

<!--        <div class="tab-content" id="nav-tabContent">-->

<!--        &lt;!&ndash;  <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">...</div>&ndash;&gt;-->
<!--          <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">Cuentas</div>-->
<!--          <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" style="display:block;">-->
<!--              <index-sucursal></index-sucursal>-->
<!--          </div>-->
<!--        </div>-->
<!--                                 </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="modal-footer">-->
<!--                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>-->
<!--                        </div>-->

<!--                </div>-->
<!--            </div>-->
<!--        </div>-->



<script>
    import IndexSucursal from "./sucursal/Index";
    export default {
        name: "banco-edit",
        components: {IndexSucursal},
        props: ['id'],
        data(){
            return{
                bancos: null,
                cargando: false,
            }
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('cadeco/banco/SET_BANCO', null);
                return this.$store.dispatch('cadeco/banco/find', {
                    id: this.id
                }).then(data => {
                    this.$store.commit('cadeco/banco/SET_BANCO', data);
                    $(this.$refs.modal).modal('show')
                })
            }
        },
        computed: {
            banco() {
                return this.$store.getters['cadeco/banco/currentBanco']
            }
        }
    }
</script>

<style scoped>

</style>
