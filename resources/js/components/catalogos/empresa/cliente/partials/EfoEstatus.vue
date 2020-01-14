<template>
    <small v-if="value.estado.id == 2 || value.estado.id == 0" class="badge" :class="{'badge-warning': value.estado.id == 2, 'badge-danger' : value.estado.id == 0 }">
        {{value.estado.descripcion}}
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
