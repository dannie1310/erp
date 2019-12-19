<template>
    <div class="btn-group">
        <ShowVenta  v-if="$root.can('consultar_venta')" v-bind:id="value.id" v-bind:pagina="value.pagina"></ShowVenta>
        <DescargaPDF  v-if="$root.can('consultar_venta')" v-bind:id="value.id" v-bind:pagina="value.pagina"></DescargaPDF>
        <Cancelar  v-if="$root.can('cancelar_venta')" v-bind:id="value.id" v-bind:pagina="value.pagina" v-bind:borrar="value.borrar"></Cancelar>
    </div>
</template>

<script>
    import ShowVenta from "../Show";
    import DescargaPDF from "../DescargaPDF";
    import Cancelar from "../Cancelar";

    export default {
        name: "action-buttons",
        components: { ShowVenta,DescargaPDF, Cancelar},
        props: ['value']
    }

</script>
