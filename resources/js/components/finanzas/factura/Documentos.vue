<template>
    <span>
        <button @click="init(id)" type="button" class="btn  btn-success float-right" title="Agregar" :disabled="cargando">
        <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>Agregar
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-factura"> <i class="fa fa-plus"></i> Items Pendientes de Facturar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="Object.keys(items).length > 0">
                        <div class="row">
                            <nav>
                                <nav>
                                    <div class="nav nav-tabs" style="align:center" id="nav-tab" role="tablist">
                                        <a aria-controls="nav-items" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-items"
                                        id="nav-items-tab" role="tab">Items Pendientes</a>
                                        <a aria-controls="nav-anticipos" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-anticipos" 
                                        id="nav-anticipos-tab" role="tab">Anticipos</a>
                                        <a aria-controls="nav-subcontratos" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-subcontratos"
                                        id="nav-subcontratos-tab" role="tab">Subcontratos</a>
                                        <a aria-controls="nav-renta" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-renta"
                                        id="nav-renta-tab" role="tab">Renta de Maq/Equipo</a>
                                        <a aria-controls="nav-lista" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-lista"
                                        id="nav-lista-tab" role="tab">Lista de Raya/Prestaciones</a>
                                        <a aria-controls="nav-descuentos" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-descuentos"
                                        id="nav-descuentos-tab" role="tab">+/- Desc/Recargos</a>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div aria-labelledby="nav-items-tab" class="tab-pane fade show active" id="nav-items" role="tabpanel">
                                        <PendientesTab v-bind:items="items" />
                                    </div>
                                    <div aria-labelledby="nav-anticipos-tab" class="tab-pane fade" id="nav-anticipos" role="tabpanel">
                                        <AnticiposTab  v-bind:items="items" />
                                    </div>
                                    <div aria-labelledby="nav-subcontratos-tab" class="tab-pane fade" id="nav-subcontratos" role="tabpanel">
                                        <SubcontratosTab  v-bind:items="items" />
                                    </div>
                                    <div aria-labelledby="nav-renta-tab" class="tab-pane fade" id="nav-renta" role="tabpanel">
                                        <RentaTab v-bind:items="items"  />
                                    </div>
                                    <div aria-labelledby="nav-lista-tab" class="tab-pane fade" id="nav-lista" role="tabpanel">
                                        <ListaTab  v-bind:items="items" />
                                    </div>
                                    <div aria-labelledby="nav-descuentos-tab" class="tab-pane fade" id="nav-descuentos" role="tabpanel">
                                        <DescuentosTab v-bind:items="items"  />
                                    </div>
                                </div>
                            </nav>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="aceptar()" >Cerrar</button>
                    <button type="submit" class="btn btn-primary" data-dismiss="modal" @click="aceptar()" >Aceptar</button>
                </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import PendientesTab from './tabs-documentos/PendientesTab';
import AnticiposTab from './tabs-documentos/AnticiposTab';
import SubcontratosTab from './tabs-documentos/SubcontratosTab';
import RentaTab from './tabs-documentos/RentaTab';
import ListaTab from './tabs-documentos/ListaTab';
import DescuentosTab from './tabs-documentos/DescuentosTab';
export default {
    name: "revision-documentos",
    components:{SubcontratosTab, PendientesTab, AnticiposTab, RentaTab, ListaTab, DescuentosTab},
    props: ['id', 'items'],
    data() {
        return {
            cargando: false,
            tipo_item:'',
        }
    },
    methods: {
        init(){
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show') 
        },
        tipos_items(){
            return {
                'pendientes': "Items Pendientes",
                'anticipos': "Anticipos     ",
                'subcontratos': "Subcontratos       ",
                'renta': "Renta de Maquinaria/Equipo",
                'lista': "Lista de Raya/Prestaciones",
                'descuentos': "+/- Descuentos/Recargos",
            };
        },
        aceptar(){
            this.$emit('created', this.items);
        },
    },
    computed: {
        // items(){
        //     return this.$store.getters['finanzas/factura/items_revision'];
        // },
    },
}
</script>

<style>

</style>