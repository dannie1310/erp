<template>
    <div class="btn-group">
        <ProveedorContratistaShow v-bind:id="value.id" />
        
    </div>
</template>

<script>
    import ProveedorContratistaShow from "../Show";
    export default {
        name: "action-buttons",
        components: {ProveedorContratistaShow},
        props: ['value'],
    }
</script>

<style scoped>

</style>
