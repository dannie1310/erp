<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-secondary" title="Ver Sucursal">
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
                            <div>


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
