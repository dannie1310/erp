<template>

    <span>
    <nav>
                 <div class="col-md-12"  v-if="banco">
                                              <div class="form-group">
                                                        <label><b>Banco:</b></label>
                                                        {{ banco.razon_social }}
                                                    </div>
                                     </div>

                                          <nav>
                      <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <!--    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Identificación</a>-->
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Cuentas</a>
                        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Sucursales</a>
                      </div>
                    </nav>

                <div class="tab-content" id="nav-tabContent">

                <!--  <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">...</div>-->
                  <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                      <index-cuenta v-bind:id="id"></index-cuenta>
                  </div>
                  <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" style="display:block;">
                      <index-sucursal  v-bind:id="id"></index-sucursal>
                  </div>
                </div>
    </nav>
    </span>

</template>
<script>
    import IndexCuenta from "./cuenta/Index";
    import IndexSucursal from "./sucursal/Index";
    export default {
        name: "banco-edit",
        components: {IndexSucursal, IndexCuenta},
        props: ['id'],
        data(){
            return{
                bancos: null,
                cargando: false,
            }
        },
        mounted() {
            this.find()
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
