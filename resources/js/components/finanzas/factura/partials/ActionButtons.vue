<template>
    <span>
        <div class="btn-group">
            <FacturaShow v-if="value.show" v-bind:id="value.id" />
            <!-- <router-link  :to="{ name: 'factura-revisar', params: {id: value.id}}"  type="button" class="btn btn-sm btn-outline-dark" title="Revisar">
                <i class="fa fa-tasks"></i>
            </router-link> -->
            <button @click="modalRevision" v-if="value.revisar || value.revisar_varios"  type="button" class="btn btn-sm btn-outline-secondary" title="Revisar"><i class="fa fa-tasks"></i></button>
            <PDF v-bind:id="value.id" @click="value.id" v-if="$root.can('consultar_factura')"></PDF>
            <CFDI v-bind:id="value.id" @click="value.id" ></CFDI>
            <Eliminar v-if="value.borrar" v-bind:id="value.id" v-bind:pagina="value.pagina" />
            <Revertir v-if="value.revertir" v-bind:id="value.id" />
            <Relaciones v-bind:transaccion="value.transaccion"/>
            <router-link  :to="{ name: 'factura-documentos', params: {id: value.id}}" v-if="$root.can('consultar_factura') && $root.can('consultar_archivos_transaccion')" type="button" class="btn btn-sm btn-outline-primary" title="Ver">
                <i class="fa fa-folder-open"></i>
            </router-link>
        </div>
         <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-factura"> <i class="fa fa-tasks"></i> SELECCIONAR TIPO DE REVISIÓN FACTURA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <router-link  :to="{ name: 'factura-varios-revisar', params: {id: value.id}}" type="button" data-dismiss="modal" class="btn btn-sm btn-outline-dark" title="Revisar" v-if="value.revisar_varios">
                                    <i class="fa fa-tasks"></i> Factura de Varios
                                </router-link>
                                <router-link  :to="{ name: 'factura-revisar', params: {id: value.id}}"  type="button" data-dismiss="modal" class="btn btn-sm btn-outline-dark" title="Revisar" v-if="value.revisar">
                                    <i class="fa fa-tasks"></i> Revisión de Facturas
                                </router-link>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
         </div>
    </span>
</template>

<script>
    import PDF from '../FormatoCR';
    import FacturaShow from "../Show";
    import Eliminar from '../Eliminar';
    import Revertir from "../Revertir";
    import Relaciones from "../../../globals/ModalRelaciones";
    import CFDI from "../CFDI";
    export default {
        name: "action-buttons",
        components: {Revertir, PDF, FacturaShow, Eliminar, Relaciones, CFDI},
        props: ['value'],
        methods: {
           modalRevision(){
               if(this.value.revisar_varios || this.value.revisar){
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
               }
                
           },
           cerrar(){
               $(this.$refs.modal).modal('hide')
           }
        },
    }
</script>

<style scoped>

</style>
