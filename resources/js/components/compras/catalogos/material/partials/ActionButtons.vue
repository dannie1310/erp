<template>
    <span>
        <div class="btn-group">
            <Eliminar v-if="$root.can('eliminar_insumo_material')" v-bind:material="value.id" v-bind:pagina="value.pagina" v-bind:borrar="value.borrar"></Eliminar>
            <Edit v-if="$root.can('editar_insumo_material')" v-bind:material="value.id" v-bind:pagina="value.pagina" v-bind:update="value.update"></Edit>
        </div>
    </span>
</template>

<script>
    import Eliminar from "../Eliminar";
    import Edit from "../Edit";
    export default {
        name: "ActionButtons",
        components: { Edit, Eliminar},
        props: ['value']
    }
</script>

<style scoped>

</style>
