<template>
    <span>
        <button @click="find()" type="button"class="btn btn-sm btn-outline-secondary" title="Ver Banco">
<i class="fa fa-eye"></i>
</button>
<div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> INFORMACIÓN DE BANCO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div v-if="banco">
                        <div class="row" v-if="banco">
                            <div class="col-md-12" v-if="banco.ctgBanco">
                                <div class="form-group">
                                    <label><b>Clave: </b></label>
                                    {{ banco.ctgBanco.clave }}
                                </div>
                            </div>
                            <div class="col-md-12" v-if="banco.razon_social">
                                <div class="form-group error-content">
                                    <div class="form-group">
                                        <label><b>Banco:</b></label>
                                        {{ banco.razon_social }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6" v-if="banco.ctgBanco">
                                <div class="form-group">
                                    <label><b>Descripción Corta: </b></label>
                                    {{ banco.ctgBanco.descripcion_corta }}
                                </div>
                            </div>
                            <div class="col-md-6" v-if="banco.ctgBanco">
                                <div class="form-group">
                                    <label><b>Nombre Corto: </b></label>
                                    {{ banco.ctgBanco.nombre_corto }}
                                </div>
                            </div>



                            <div class="col-md-6" v-if="banco.usuario">
                                <div class="form-group">
                                    <label><b>Registró: </b></label>
                                    {{ banco.usuario.nombre }}
                                </div>
                            </div>
                            <div class="col-md-6" v-if="banco.FechaHoraRegistro">
                                <div class="form-group">
                                    <label><b>Fecha y Hora: </b></label>
                                    {{ banco.FechaHoraRegistro }}
                                </div>
                            </div>

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
        name: "banco-show",
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
                    id: this.id,
                    params:{
                        include: ['ctgBanco','usuario']
                    }
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
