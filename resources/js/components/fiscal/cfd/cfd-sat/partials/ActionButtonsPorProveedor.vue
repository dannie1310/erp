<template>
    <div class="btn-group">
        <ComunicadoProveedoresREP v-bind:id="value.id"></ComunicadoProveedoresREP>
    </div>
</template>

<script>

    import ComunicadoProveedoresREP from "../ComunicadoProveedoresREP";
    export default {
        name: "ActionButtonsPorProveedor",
        components: {ComunicadoProveedoresREP},
        props: ['value']
    }
</script>

<style scoped>

</style>
