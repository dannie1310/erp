<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'comprobante-fondo-show', params: {id: value.id}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </router-link>
        <Delete  v-bind:id="value.id" v-if="value.eliminar && $root.can('eliminar_ajuste_positivo')" />
    </div>
</template>

<script>
    import Delete from "../Delete";
    export default {
        name: "ActionButtons",
        components : {Delete},
        props: ['value']
    }
</script>

<style scoped>

</style>
