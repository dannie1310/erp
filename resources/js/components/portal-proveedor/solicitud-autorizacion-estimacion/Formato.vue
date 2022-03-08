<template>
        <span>
            <button @click="init" type="button" class="btn btn-sm btn-outline-primary" title="Ver Formato Solicitud PDF"><i class="fa fa-file-pdf-o"></i> </button>

             <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
                 <div class="modal-dialog modal-lg" id="mdialTamanio">
                     <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Formato Solicitud de Avance</h4>
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
        props: ['id','base'],
        methods: {
            init() {
                this.pdf()
            },
            pdf(){
                var url = '/api/portal-proveedor/solicitud-autorizacion-avance/' + this.id +'/formato?db=' + atob(this.base) +'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Formato Estimación</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            }
        }
    }
</script>
