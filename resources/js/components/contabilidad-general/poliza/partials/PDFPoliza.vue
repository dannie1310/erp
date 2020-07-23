<template>
    <span>
        <button @click="init" type="button" class="btn btn-outline-success btn-sm" title="PDF Póliza">
            <i class="fa fa-file-pdf-o"></i>
        </button>

        <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
            <div class="modal-dialog modal-lg" id="mdialTamanio">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Impresión de Pólizas (Actual)</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    </div>
                    
                    <div class="modal-body modal-lg" id="pdf_frame">
                        <div class="row">
                            <div class="col-md-12">
                                <button @click="pdfB()" type="button" class="btn btn-primary pull-right" style="margin-left:5px" title="Ver Póliza">
                                    <i class="fa fa-file-pdf-o"></i>Ver PDF B
                                </button>
                                <button @click="pdfA()" type="button" class="btn btn-primary pull-right" title="Ver Póliza">
                                    <i class="fa fa-file-pdf-o"></i>Ver PDF A
                                </button>
                            </div>
                        </div>
                        <br>
                        <div ref="body" ></div>

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
        props: ['id', 'id_empresa'],
        data() {
            return {
            }
        },
        methods: {
            init() {
                $(this.$refs.body).html('');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            pdfA(){
                var url = '/api/contabilidad-general/poliza/' + this.id +'/pdf?'+'&id_empresa='+this.id_empresa+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" style="height: 800px" height="100%" width="100%">Formato Polizas</iframe>');
            },
            pdfB(){
                var url = '/api/contabilidad-general/poliza/' + this.id +'/pdf-b?'+'&id_empresa='+this.id_empresa+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" style="height: 800px" height="100%" width="100%">Formato Polizas</iframe>');
            }
        }
    }
</script>
