<template>
    <div class="btn-group">
        <Show  v-if="$root.can('consultar_salida_almacen')" v-bind:id="value.id" v-bind:pagina="value.pagina" />
        <Edit v-if="value.edit" v-bind:id="value.id" v-bind:pagina="value.pagina" />
        <Eliminar v-if="$root.can('eliminar_salida_almacen')" v-bind:id="value.id" v-bind:pagina="value.pagina"/>
        <PDF v-bind:id="value.id" @click="value.id"  v-if="$root.can('consultar_salida_almacen')"></PDF>
        <router-link  :to="{ name: 'salida-almacen-documentos', params: {id: value.id}}" v-if="$root.can('consultar_salida_almacen') && $root.can('consultar_archivos_transaccion')" type="button" class="btn btn-sm btn-outline-primary" title="Ver">
            <i class="fa fa-folder-open"></i>
        </router-link>
    </div>
</template>

<script>
    import Eliminar from "../delete";
    import PDF from "../FormatoSalidaAlmacen";
    import Show from "../Show";
    import Edit from "../Edit";

    export default {
        name: "action-buttons",
        components: {Edit, Eliminar, Show,PDF},
        props: ['value']
    }

</script>
