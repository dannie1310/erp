<template>
    <span>
        <div class="modal fade" ref="modal_global" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body modal-xl" style="height: 800px" ref="body_modal_global">
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
        name: "modal-global",
        props: ['ruta'],
        data(){
            return{
                cargando: false
            }
        },
        mounted() {

        },
        methods: {
            ver() {
                var url = this.ruta;
                $(this.$refs.body_modal_global).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%"></iframe>');
                $(this.$refs.modal_global).appendTo('body')
                $(this.$refs.modal_global).modal('show');
            }
        },
    }
</script>

<style scoped>

</style>
