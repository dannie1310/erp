<template>
    <small class="badge" :class="{ 'badge-success': value.estado == 1,  'badge-primary': value.estado == 0 }">
        {{ value.descripcion }}
    </small>
</template>

<script>
    export default {
        name: "CargaMasivaEstatus",
        props: ['value']
    }
</script>

<style scoped>

</style>
