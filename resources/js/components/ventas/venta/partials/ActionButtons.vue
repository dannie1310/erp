<template>
    <div class="btn-group">
        <ShowVenta  v-if="$root.can('consultar_venta')" v-bind:id="value.id" v-bind:pagina="value.pagina"></ShowVenta>
    </div>
</template>

<script>
    import ShowVenta from "../Show";

    export default {
        name: "action-buttons",
        components: { ShowVenta},
        props: ['value']
    }

</script>
