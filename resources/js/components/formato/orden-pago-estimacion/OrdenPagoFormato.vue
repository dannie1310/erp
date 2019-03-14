<template v-if="id != ''">
        <span>
            <button @click="init"  type="submit" class="btn btn-primary">Ver Formato</button>

             <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
                 <div class="modal-dialog modal-lg" id="mdialTamanio">
                     <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                            <h4 class="modal-title">Orden de Pago Estimación</h4>
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
    import pdf from 'vue-pdf'

    export default {
        components: {
            pdf
        },
        props: ['id'],
        data() {
            return {
            }
        },
        methods: {
            init() {
                this.pdf()

            },
            pdf(){
               var url = '/api/formato/orden-pago-estimacion/' + this.id;
              //  var url = 'http://www.ti.com/lit/ds/symlink/ads7853.pdf';
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="99.6%">d</iframe>');
                $(this.$refs.modal).modal('show');
            }
        }
    }
</script>
