<template>
    <span>
        <button @click="init" type="button" class="btn btn-sm btn-outline-primary" title="Ver Formato PDF de Invitación"><i class="fa fa-file-pdf-o"></i><span v-if="texto">{{texto}}</span> </button>
        <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
             <div class="modal-dialog modal-lg" id="mdialTamanio">
                 <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Formato de Invitación a Cotizar</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    </div>
                    <div class="modal-body modal-lg" style="height: 800px" ref="body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times-circle"></i>
                            Cerrar
                        </button>
                    </div>
                 </div>
             </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "FormatoInvitacionCotizacionContrato",
        props: ['id', 'db', 'id_obra', 'texto'],
        methods: {
            init() {
                this.pdf()
            },
            pdf(){
                let vdb = (this.db)?this.db:this.$session.get('db');
                let vid_obra = (this.id_obra)?this.id_obra:this.$session.get('id_obra');
                var url = '/api/padron-proveedores/invitacion/pdf/' + this.id +'?db=' + vdb + '&idobra=' +vid_obra+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  height="100%" width="100%">Formato de Invitación a Cotizar</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            }
        }
    }
</script>

<style scoped>

</style>
