<template>
    <small v-if="value.id == 2 || value.id == 0" class="badge" :class="{'badge-warning': value.id == 2, 'badge-danger' : value.id == 0 }">
        {{value.descripcion}}
    </small>
</template>

<script>
    export default {
        name: "destajista-efo",
        props: ['value']
    }
</script>

<style scoped>

</style>
