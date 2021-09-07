<template>
    <span>
        <button @click="init" v-if="$root.can('consultar_variacion_volumen')" type="button" :class="txt ?'':'btn-sm'" class="btn btn-outline-primary float-right" title="Ver Formato PDF">
            <i class="fa fa-file-pdf-o"></i>
            <span v-if="txt">{{txt}}</span>
        </button>

         <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
             <div class="modal-dialog modal-lg" id="mdialTamanio">
                 <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Formato de Solicitud de Concepto Extraordinario</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    </div>
                    <div class="modal-body modal-lg" style="height: 650px" ref="body">

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
        name: "extraordinario-pdf",
        props: ['id', 'txt'],
        methods: {
            init() {
                this.pdf()
            },
            pdf(){
                var url = '/api/control-presupuesto/extraordinario/' + this.id +'/formato?db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra')+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Formato Estimación</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            }
        }
    }
</script>
