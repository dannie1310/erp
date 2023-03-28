<template>
    <span>
        <button @click="pdf" type="button" class="btn btn-sm btn-outline-primary" title="Ver Formato PDF"><i class="fa fa-file-pdf-o"></i> </button>
        <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
             <div class="modal-dialog modal-lg" id="mdialTamanio">
                 <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tabla comparativa de presupuestos</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    </div>
                    <div class="modal-body modal-lg" style="height: 800px" ref="body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times-circle"></i>
                            Cerrar</button>
                    </div>
                 </div>
             </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "FormatoTablaComparativa",
        props: ['id'],
        methods: {
            pdf(){
                var url = '/api/contratos/contrato-proyectado/'+this.id +'/comparativa-cotizaciones/pdf?db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra')+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Tabla comparativa de cotizaciones</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            }
        }
    }
</script>

<style scoped>

</style>
