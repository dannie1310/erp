<template>
    <div class="btn-group">
        <ShowVenta  v-if="$root.can('consultar_venta')" v-bind:id="value.id" v-bind:pagina="value.pagina"></ShowVenta>
        <DescargaPDF  v-if="$root.can('consultar_venta')" v-bind:id="value.id" v-bind:pagina="value.pagina"></DescargaPDF>
        <Eliminar  v-if="$root.can('eliminar_venta') || true" v-bind:id="value.id" v-bind:pagina="value.pagina"></Eliminar>
    </div>
</template>

<script>
    import ShowVenta from "../Show";
    import DescargaPDF from "../DescargaPDF";
    import Eliminar from "../Delete";

    export default {
        name: "action-buttons",
        components: { ShowVenta,DescargaPDF, Eliminar},
        props: ['value']
    }

</script>
