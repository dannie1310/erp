<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'comparativa-cotizacion-compra-consultar', params: {id: value.id}}" v-if="$root.can('consultar_cotizacion_compra')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver Comparativa">
            <i class="fa fa-less-than-equal"></i>
        </router-link>
        <ConsultaEstado v-bind:id="value.id" />
        <PDF v-bind:id="value.id" v-if="$root.can('consultar_cotizacion_compra')" />
    </div>
</template>
<script>
    import PDF from '../FormatoTablaComparativa';
    import ConsultaEstado from "../ConsultaEstado";
    export default {
        name: "solicitud-compra-buttons",
        components: {ConsultaEstado, PDF},
        props: ['value'],
        methods: {

        }
    }
</script>

<style scoped>

</style>
