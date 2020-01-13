<template>
    <small v-if="value.estado == 2 || value.estado == 0" class="badge" :class="{'badge-warning': value.estado == 2, 'badge-danger' : value.estado == 0 }">
        {{value.ctg_estado.descripcion}}
    </small>

</template>

<script>
    export default {
        name: "EfoEstatus",
        props: ['value']
    }
</script>

<style scoped>

</style>
