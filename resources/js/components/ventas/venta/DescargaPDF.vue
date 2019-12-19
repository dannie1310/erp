<template>
    <span>
        <button @click="mostrar" type="button" class="btn btn-sm btn-outline-primary" title="Ver PDF">
            <i class="fa fa-file-pdf-o"></i>
        </button>
        <div class="modal fade" ref="modal1" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-paper-plane"></i>&nbsp;DESCARGAR PDF</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td >Formato Venta</td>
                                                <td class="icons">
                                                    <button @click="pdf_venta" type="button" class="btn btn-sm btn-outline-secondary" title="PDF Venta">
                                                        <i class="fa fa-download"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td >Formato Factura</td>
                                                <td class="icons">
                                                    <button @click="pdf_factura" type="button" class="btn btn-sm btn-outline-secondary" title="PDF Factura">
                                                        <i class="fa fa-download"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
                 <div class="modal-dialog modal-lg" id="mdialTamanio">
                     <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Formato Venta</h4>
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
    name: "venta-pdf",
        props: ['id','pagina'],
        data() {
            return {
                cargando: false,
            }
        },
        methods: {
            mostrar(){
                $(this.$refs.modal1).modal('show');
            },
            pdf_venta(){
                $(this.$refs.modal1).modal('hide');
                var url = '/api/ventas/venta/' + this.id +'/pdf_venta?db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra')+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Formato Estimación</iframe>');
                $(this.$refs.modal).modal('show');
            },
            pdf_factura(){
                console.log('descarga pdf factura');
            }
        }

}
</script>
<style>
    .icons
    {
        text-align: center;
    }
</style>