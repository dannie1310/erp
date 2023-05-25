<template>
    <div class="btn-group">
        <router-link :to="{ name: 'consultar-materiales-almacen', params: {id: this.value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Ver Materiales">
            <i class="fa fa-boxes"></i>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "ActionButtons",
        props: ['value']
    }
</script>

<style scoped>

</style>
