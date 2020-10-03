<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-success" :disabled="cargando_relaciones" title="Ver Relaciones">
            <i class="fa fa-project-diagram" v-if="!cargando_relaciones"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa project-diagram"></i> RELACIONES</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="relaciones">
                        <div class="row">
                            <div class="col-md-12">
                                <template v-for="(relacion, i) in relaciones">
                                    <router-link :to="{name: '', params: {id: relacion.id}}">
                                        <span class="info-box-text">{{relacion.tipo}} <i class="fa fa-arrow-circle-o-right" /></span>
                                    </router-link>
                                </template>

                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times-circle"></i>
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "Relaciones",
    props: ['relaciones','presentacion'],
    data(){
        return{
            cargando_relaciones: false,
            configuracion: ''
        }
    },
    methods: {
        find() {
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show')
        },
    },
}
</script>

<style scoped>

</style>
