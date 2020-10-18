<template>
    <div class="btn-group">
        <router-link :to="{ name: 'cotizacion-show', params: {id: this.value.id}}" v-if="$root.can('consultar_cotizacion_compra')" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <DescargarLayout v-if="$root.can('descargar_layout_cotizacion_compra') && $root.can('editar_cotizacion_compra') && value.edit" v-bind:id="value.id"></DescargarLayout>
        <CargaLayout v-if="$root.can('cargar_layout_cotizacion_compra') && $root.can('editar_cotizacion_compra')  && value.edit" v-on:back="layout" v-bind:id="value.id"></CargaLayout>
        <router-link :to="{ name: 'cotizacion-edit', params: {id: this.value.id, xls: this.xls}}" v-if="$root.can('editar_cotizacion_compra') && value.edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <Delete v-bind:id="value.id" v-if="value.delete"/>
        <PDF v-bind:id="value.id" />
        <Relaciones v-bind:transaccion="value.transaccion"/>
        <router-link  :to="{ name: 'cotizacion-documentos', params: {id: value.id}}" v-if="$root.can('consultar_cotizacion_compra')" type="button" class="btn btn-sm btn-outline-primary" title="Ver">
            <i class="fa fa-folder-open"></i>
        </router-link>
    </div>
</template>
<script>
import Delete from "../Delete";
import DescargarLayout from '../DescargaLayout';
import CargaLayout from '../CargaLayout';
import PDF from '../FormatoTablaComparativa';
import Relaciones from "../../../globals/ModalRelaciones";
    export default {
        name: "cotizacion-buttons",
        components: {Delete, DescargarLayout, CargaLayout, PDF, Relaciones},
        props: ['value'],
        data()
        {
            return {
                xls: null
            }
        },

        methods: {
            layout(dat)
            {
                this.xls = (dat) ? dat : null;
                if(this.xls)
                {
                    this.edit();
                }
            }
        }
    }
</script>

<style scoped>

</style>
