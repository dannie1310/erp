<template>
    <span>
        <button @click="ver()" type="button" class="btn btn-sm btn-primary" :disabled="cargando" title="Ver">
            <i class="fa fa-eye" v-if="!cargando && !value.numero_folio"></i>
            <i style="width:40px;" v-else-if="!cargando && value.numero_folio">{{value.numero_folio}}</i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> CONSULTA DE CONTRATO PROYECTADO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="height: 800px" ref="body">
                        <div class="row" >
                            <div class="col-md-12">
                                <div class="spinner-border text-success" role="status">
                                   <span class="sr-only">Cargando...</span>
                                </div>
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
        name: "contrato-proyectado-show",
        props: ['value'],
        data(){
            return{
                cargando: false
            }
        },
        methods: {
            ver() {
                var url = '/sao/modal/contrato_proyectado/'+this.value.id;
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%"></iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            }
        },
    }
</script>

<style scoped>

</style>
