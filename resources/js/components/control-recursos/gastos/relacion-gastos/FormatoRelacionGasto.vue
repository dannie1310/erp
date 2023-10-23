<template>
        <span>
            <button  @click="init" type="button" class=" btn btn-sm btn-outline-primary" title="Ver Formato PDF"><i class="fa fa-file-pdf-o"></i></button>

             <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
                 <div class="modal-dialog modal-lg" id="mdialTamanio">
                     <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Formato de Relación de Gastos</h4>
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                        </div>
                        <div class="modal-body modal-lg" style="height: 800px" ref="body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                     </div>
                 </div>
             </div>
        </span>
</template>

<script>
    export default {
        props: ['id'],
        methods: {
            init() {
                this.pdf()
            },
            pdf(){
                var url = '/api/control-recursos/relacion-gasto/' + this.id +'/formato-relacion?&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Formato Relación</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            }
        }
    }
</script>
