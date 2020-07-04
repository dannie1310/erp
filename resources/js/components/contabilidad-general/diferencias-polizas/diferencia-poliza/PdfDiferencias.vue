<template>
    <span>
        <button @click="init" type="button" class="btn btn-sm btn-outline-primary float-right" style="margin-top: 5px" title="Ver Formato PDF" :disabled="value.cargando || value.informe.length == 0">
            <i class="fa fa-file-pdf-o"></i> PDF
        </button>

         <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
             <div class="modal-dialog modal-lg" id="mdialTamanio">
                 <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Formato de Informe de Diferencias en Pólizas</h4>
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
        props: ['value'],
        methods: {
            init() {
                this.pdf()
            },
            pdf(){
                let info = '&id_empresa='+  this.value.id_empresa + 
                    '&sin_solicitud_relacionada=' + this.value.sin_solicitud_relacionada +
                    '&solo_diferencias_activas=' + this.value.solo_diferencias_activas +
                    '&con_solicitud_relacionada=' + this.value.con_solicitud_relacionada +
                    '&no_solo_diferencias_activas=' + this.value.no_solo_diferencias_activas +
                    '&tipo_agrupacion=' + this.value.tipo_agrupacion ;
                var url = '/api/contabilidad-general/incidente-poliza/pdfDiferencias?access_token='+this.$session.get('jwt')+info;
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Formato Estimación</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            }
        }
    }
</script>
