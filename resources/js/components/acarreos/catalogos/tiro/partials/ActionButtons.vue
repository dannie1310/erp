<template>
    <asignar-concepto v-bind:id="value.id" v-if="value.concepto" />
</template>

<script>
    import AsignarConcepto from "../AsignarConcepto";
    export default {
        name: "ActionButtons",
        components: {AsignarConcepto},
        props: ['value'],
    }
</script>

<style scoped>

</style>
