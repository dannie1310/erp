<template>
    <span>
        <button type="submit" class="btn btn-primary" @click="imprimir" :disabled="id == null ? true : false"><i class="fa fa-barcode"></i>Imprimir </button>
        <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
            <div class="modal-dialog modal-lg" id="mdialTamanio">
                <div class="modal-content">
                   <div class="modal-header">
                         <h4 class="modal-title"><i class="fa fa-file-pdf-o"></i> Formato de Impresión de Etiquetas</h4>
                         <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                   </div>
                   <div class="modal-body modal-lg" style="height: 800px" ref="body">

                   </div>
                   <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i>Cerrar</button>
                   </div>
                </div>
            </div>
        </div>
   </span>
</template>

<script>
export default {
    name: "FormatoImpresionEtiqueta",
    props: ['id', 'tipo'],
    methods: {
        imprimir() {
            this.pdf()
        },
        pdf(){
            var url = '/api/activo-fijo/partidaRegistrada/' + this.id +'/'+this.tipo+'/impresionEtiqueta?&access_token='+this.$session.get('jwt');
            $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Formato de Contrarecibo</iframe>');
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        }
    }
}
</script>

<style scoped>

</style>
