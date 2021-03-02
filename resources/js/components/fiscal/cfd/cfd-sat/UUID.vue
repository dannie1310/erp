<template>
    <span v-if="value">
        <span v-if="value.id!=''" style="text-decoration: underline; cursor: pointer" @click="init">{{value.uuid}}</span>
        <span v-else >{{value.uuid}}</span>

             <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
                 <div class="modal-dialog modal-lg" id="mdialTamanio">
                     <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="fa fa-file-invoice-dollar"></i> Formato de CFDI</h4>
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                        </div>
                        <div class="modal-body modal-lg" style="height: 800px" ref="body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times-circle"  ></i>
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
        name: "UUID",
        props: ['value'],
        methods: {
            init() {
                this.pdf()
            },
            pdf(){
                var url = '/api/fiscal/cfd-sat/' + this.value.id +'/cfdi-pdf?access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Formato de CFDI</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            }
        }
    }
</script>

<style scoped>

</style>
