<template>
    <div class="btn-group">
        <DatosPago v-if="value.pagar" v-bind:id="value.id" />
    </div>
</template>

<script>
    import DatosPago from "../DatosPago";
    export default {
        name: "action-buttons",
        components: {DatosPago},
        props: ['value']
    }
</script>

<style scoped>

</style>
