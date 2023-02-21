<template>
    <span>
        <button @click="verPDF" class="btn btn-sm btn-outline-primary" title="Ver Comunicado">
            <i class="fa fa-file-pdf"></i>
        </button>
        <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
            <div class="modal-dialog modal-lg" id="mdialTamanio">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Comunicado</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    </div>
                    <div class="modal-body modal-lg" style="height: 800px" ref="body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times"></i>
                            Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "ComunicadoProveedoresREP",
    props: ['id'],
    methods:{
        verPDF(){
            var url = '/api/fiscal/proveedor-rep/' + this.id + '/comunicado-pdf?&access_token=' + this.$session.get('jwt');
            $(this.$refs.body).html('<iframe src="' + url + '"  frameborder="0" height="100%" width="100%">Formato Contrato Proyectado</iframe>');
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
    }
}
</script>

<style scoped>

</style>
