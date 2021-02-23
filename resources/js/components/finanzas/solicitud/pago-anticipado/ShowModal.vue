<template>
    <span>
        <button @click="ver()" type="button" class="btn btn-sm btn-primary"  title="Ver">
            <i style="width:40px;">{{numero_folio}}</i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> SOLICITUD DE PAGO ANTICIPADO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body" ref="body" style="height: 600px">
                            <div class="row">
                                <div class="col-12">
                                    <div class="spinner-border text-success" role="status">
                                       <span class="sr-only">Cargando...</span>
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
        name: "solicitud-pago-anticipado-show",
        props: ['value','id','numero_folio'],
        methods: {
            ver() {
                var url = '/sao/modal/solicitud_pago_anticipado/'+this.id;
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%"></iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            }
        }
    }
</script>

<style scoped>

</style>
