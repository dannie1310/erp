<template>
    <span>
        <button @click="init" type="button" class="btn btn-sm btn-outline-primary" title="Ver" v-if="!texto">
            <i class="fa fa-file-pdf-o"></i>
        </button>

        <button @click="init" type="button" class="btn btn-sm btn-outline-primary" title="Ver" v-else>
            <i class="fa fa-file-pdf-o"></i>{{texto}}
        </button>

        <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
            <div class="modal-dialog modal-lg" id="mdialTamanio">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-if="descripcion">{{descripcion}}</h4>
                       <h4 class="modal-title" v-else>Ver Documento</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    </div>
                    <div class="modal-body modal-lg" style="height: 800px" ref="body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times-circle"></i>Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        props: ['id', 'url', 'descripcion','texto','base_datos','id_obra', 'metodo'],
        methods: {
            init() {
                this.pdf()
            },
            pdf(){
                var url = this.url.replace("{id}", this.id);

                if(this.base_datos)
                {
                    url = url.replace("{base_datos}", this.base_datos);
                }else{
                    url = url.replace("{base_datos}", this.$session.get('db'));
                }

                if(this.id_obra)
                {
                    url = url.replace("{id_obra}", this.id_obra);
                }else {
                    url = url.replace("{id_obra}", this.$session.get('id_obra') );
                }

                url = url.replace("{metodo}", this.metodo);

                $(this.$refs.body).html('<iframe src="'+url+'"  height="100%" width="100%">Archivo</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            }
        }
    }
</script>
