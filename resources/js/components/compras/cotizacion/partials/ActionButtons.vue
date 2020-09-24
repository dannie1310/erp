<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-bind:show="value.show"></Show>
        <DescargarLayout v-if="$root.can('descargar_layout_cotizacion_compra') && $root.can('editar_cotizacion_compra') && value.edit" v-bind:id="value.id"></DescargarLayout>
        <CargaLayout v-if="$root.can('cargar_layout_cotizacion_compra') && $root.can('editar_cotizacion_compra')  && value.edit" v-on:back="layout" v-bind:id="value.id"></CargaLayout>
        <button @click="edit" v-if="$root.can('editar_cotizacion_compra') && value.edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </button>
        <Delete v-bind:id="value.id" v-if="value.delete"/>
        <PDF v-bind:id="value.id" />
    </div>
</template>
<script>
import Show from '../Show';
import Delete from "../Delete";
import DescargarLayout from '../DescargaLayout';
import CargaLayout from '../CargaLayout';
import PDF from '../FormatoTablaComparativa';
    export default {
        name: "cotizacion-buttons",
        components: {Delete, Show, DescargarLayout, CargaLayout, PDF},
        props: ['value'],
        data()
        {
            return {
                xls: null
            }
        },

        methods: {
            edit()
            {
                this.$router.push({ name: 'cotizacion-edit', params: {id: this.value.id, xls: this.xls}});
            },
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
