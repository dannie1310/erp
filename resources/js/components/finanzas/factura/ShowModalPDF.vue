<template>
    <span>
        <button @click="init" type="button" class="btn btn-sm btn-outline-primary" title="Ver Formato PDF"><i class="fa fa-file-pdf-o"></i> </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-factura"> <i class="fa fa-file-pdf-o"></i> Seleccione Formato PDF</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <span class="pull-right">
                                    <PDF v-bind:id="id" v-bind:opciones="opciones" @click="id" v-if="$root.can('consultar_factura')"></PDF>
                                    <FormatoFV v-bind:id="id"></FormatoFV>
                                </span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i>Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
    
</template>

<script>
    import PDF from './FormatoCR';
    import FormatoFV from './FormatoFacturaVario';
    export default {
        name: "formato-seleccion",
        components: {FormatoFV, PDF},
        props: ['id', 'opciones'],
        methods: {
           init(){
                $(this.$refs.modal).appendTo('body');
                $(this.$refs.modal).modal('show');
           },
           cerrar(){
               $(this.$refs.modal).modal('hide')
           }
        },
    }
</script>

<style scoped>

</style>