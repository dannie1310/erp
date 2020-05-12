<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-bind:show="value.show"></Show>
        <CargaLayout v-on:back="layout" v-bind:id="value.id"></CargaLayout>
        <DescargarLayout v-if="$root.can('descargar_layout_cotizacion_compra')" v-bind:id="value.id"></DescargarLayout>
        <button @click="edit" v-if="$root.can('editar_cotizacion_compra')" type="button" class="btn btn-sm btn-outline-info" title="Editar">
                <i class="fa fa-pencil"></i>
        </button>
        <Delete v-bind:id="value.id" v-if="value.delete"/>
    </div>
</template>
<script>
import Show from '../Show';
import Delete from "../Delete";
import DescargarLayout from '../DescargaLayout';
import CargaLayout from '../CargaLayout';
    export default {
        name: "cotizacion-buttons",
        components: {Delete, Show, DescargarLayout, CargaLayout},
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
