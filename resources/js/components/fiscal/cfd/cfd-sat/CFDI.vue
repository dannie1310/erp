<template>
    <span v-if="this.id_ev">
            <span v-if="txt_ev" @click="init" style="cursor: pointer"
                  :style="cancelado_ev ==1?`text-decoration: line-through; color: #F00`:`text-decoration: underline; color: #003eff`"
            >
                <span v-if="corto_ev">{{txt_ev.substring(0,8)}}...</span>
                <span v-else>{{txt_ev}}</span>
            </span>
            <button @click="init" v-else type="button" class="btn btn-sm btn-outline-primary" title="Ver Formato CFDI" :disabled="cargandoEstado">
                <i class="fa fa-file-invoice-dollar"></i> </button>

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
                                <i class="fa fa-times"  ></i>
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
        name: "CFDI",
        props: ['id','txt','cancelado', 'corto', 'value'],
        data(){
            return {
                id_ev:null,
                txt_ev : null,
                cancelado_ev : null,
                corto_ev : null,
            }
        },
        computed: {
            cargandoEstado(){
                return this.$store.getters['fiscal/cfd-sat/currentEstado'];
            }
        },
        mounted() {
            if(this.value !== undefined)
            {
                this.id_ev = this.value.id;
                this.txt_ev = this.value.txt;
                this.cancelado_ev = this.value.cancelado;
                this.corto_ev = this.value.corto;
            }else{
                this.id_ev = this.id;
                this.txt_ev = this.txt;
                this.cancelado_ev = this.cancelado;
                this.corto_ev = this.corto;
            }
        },
        methods: {
            init() {
                this.pdf()
            },
            pdf(){
                var url = '/api/fiscal/cfd-sat/' + this.id_ev +'/cfdi-pdf?db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra')+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Formato de CFDI</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            }
        }
    }
</script>

<style scoped>

</style>
