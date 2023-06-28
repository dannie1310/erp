<template>
    <span>
        <div class="row">
            <div class="col-md-12" style="text-align: center">
                 <button type="button" class="btn btn-sm btn-outline-primary" title="Ver Formato CFDI" v-if="value.length>0"  @click="init">{{value.length}}</button>
                 <span v-else>{{value.length}}</span>
                 <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
                 <div class="modal-dialog " id="mdialTamanio">
                     <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="fa fa-file-invoice-dollar"></i> Lista de CFDI</h4>
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                        </div>
                        <div class="modal-body " ref="body">
                            <template v-for="(cfdi, i) in value">
                                <div class="row" v-if="!cfdi.cfdi" >
                                    <div class="col-md-12" >
                                        {{i+1}}-{{cfdi.uuid}}
                                    </div>
                                </div>
                                <div class="row" v-else >
                                    <div class="col-md-9">
                                        {{i+1}}-{{cfdi.uuid}}
                                    </div>
                                    <div class="col-md-3" style="text-align:center">
                                        <CFDI v-bind:id="cfdi.cfdi.id" @click="cfdi.cfdi.id" ></CFDI>
                                        <DescargaCFDI v-bind:id="cfdi.cfdi.id"></DescargaCFDI>
                                    </div>
                                </div>
                            </template>


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
            </div>
        </div>
    </span>
</template>

<script>

    import CFDI from "../../../cfd/cfd-sat/CFDI.vue";
    import DescargaCFDI from "../../../cfd/cfd-sat/DescargaCFDI.vue";
    export default {
        name: "lista-cfdi",
        components:{DescargaCFDI, CFDI},
        props: ['value'],
        methods: {
            init() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
        }
    }
</script>

