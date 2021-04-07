<template>
    <span>
        <button @click="init(id)" type="button" class="btn  btn-success float-right" style="margin-left:5px" title="Agregar" :disabled="cargando">
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
                    <div class="modal-body">
                        <div class="row">
                            <div class="btn-group btn-group-toggle col-md-12">
                                <label class="btn btn-outline-secondary" :class="tipo_item === Number(key) ? 'active': ''" v-for="(item, key) in tipos_items" :key="key">
                                    <input type="radio"
                                        class="btn-group-toggle"
                                        name="tipo_item"
                                        :id="'tipo_item' + key"
                                        :value="key"
                                        autocomplete="on"
                                        v-model.number="tipo_item">
                                    {{ item }}
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <PendientesTab v-bind:items="items" v-bind:id_moneda="id_moneda" v-bind:cambios="cambios" v-if="tipo_item == 1" />
                                <AnticiposTab  v-bind:items="items" v-bind:id_moneda="id_moneda" v-bind:cambios="cambios" v-if="tipo_item == 2" />
                                <SubcontratosTab  v-bind:items="items" v-bind:id_moneda="id_moneda" v-bind:cambios="cambios" v-if="tipo_item == 3" />
                                <RentaTab v-bind:items="items" v-bind:id_moneda="id_moneda" v-bind:cambios="cambios"  v-if="tipo_item == 4" />
                                <ListaTab  v-bind:items="items" v-bind:id_moneda="id_moneda" v-bind:cambios="cambios" v-if="tipo_item == 5" />
                                <DescuentosTab v-bind:items="items" v-bind:id_moneda="id_moneda" v-bind:cambios="cambios"  v-if="tipo_item == 6" />
                            </div>
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
    props: ['id', 'items', 'id_moneda', 'cambios'],
    data() {
        return {
            cargando: false,
            tipo_item:1,
            tipos_items:{
                1:'Items Pendientes',
                2:'Anticipos',
                3:'Subcontratos',
                4:'Renta de Maq/Equipo',
                5:'Lista de Raya/Prestaciones',
                6:'+/-Desc/Recargos',
            }
        }
    },
    methods: {
        init(){
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show') 
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