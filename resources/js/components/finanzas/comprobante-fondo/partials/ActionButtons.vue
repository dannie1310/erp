<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'comprobante-fondo-show', params: {id: value.id}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
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
