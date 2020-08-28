<template>
    <div class="btn-group">
        <DatosPago v-if="value.pagar" v-bind:id="value.id" />
        <Delete v-if="value.delete" v-bind:id="value.id" />
    </div>
</template>

<script>
    import DatosPago from "../DatosPago";
    import Delete from "../Delete";
    export default {
        name: "action-buttons",
        components: {Delete, DatosPago},
        props: ['value']
    }
</script>

<style scoped>

</style>
