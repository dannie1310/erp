<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'presupuesto-show', params: {id: value.id}}" v-if="$root.can('consultar_presupuesto_contratista')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </router-link>
        <DescargaLayout v-if="$root.can('descargar_layout_presupuesto_contratista')" v-bind:id="value.id"></DescargaLayout>
        <CargaLayout v-if="$root.can('cargar_layout_presupuesto_contratista')" v-on:back="layout" v-bind:id="value.id"></CargaLayout>
        <router-link  :to="{ name: 'presupuesto-edit', params: {id: value.id}}" v-if="$root.can('editar_presupuesto_contratista')" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <PDF v-bind:id="value.id" />
        <Eliminar v-if="$root.can('eliminar_presupuesto_contratista')" v-bind:id="value.id"></Eliminar>
        <Relaciones v-bind:transaccion="value.transaccion"/>
        <router-link  :to="{ name: 'presupuesto-documentos', params: {id: value.id}}" v-if="$root.can('consultar_presupuesto_contratista') && $root.can('consultar_archivos_transaccion')" type="button" class="btn btn-sm btn-outline-primary" title="Ver Documentos">
            <i class="fa fa-folder-open"></i>
        </router-link>
    </div>
</template>
<script>
import Show from '../Show';
import Eliminar from '../Delete';
import DescargaLayout from '../DescargaLayout';
import CargaLayout from '../CargaLayout';
import PDF from '../FormatoTablaComparativa';
import Relaciones from "../../../globals/ModalRelaciones";
    export default {
        name: "presupuesto-buttons",
        components: {Show, Eliminar, DescargaLayout, CargaLayout, PDF, Relaciones},
        props: ['value'],

        methods: {
            edit()
            {
                this.$router.push({ name: 'presupuesto-edit', params: {id: this.value.id, xls: this.xls}});
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
