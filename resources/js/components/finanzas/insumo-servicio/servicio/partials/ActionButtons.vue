<template>
    <span>
        <div class="btn-group">
            <Eliminar v-if="$root.can('eliminar_insumo_servicio')" v-bind:servicio="value.id" v-bind:pagina="value.pagina" v-bind:borrar="value.borrar"></Eliminar>
            <Editar v-if="$root.can('editar_insumo_servicio')" v-bind:servicio="value.id" v-bind:pagina="value.pagina" v-bind:update="value.update"></Editar>
        </div>
    </span>
</template>

<script>
    import Eliminar from '../Eliminar';
    import Editar from '../Editar';
    export default {
        name: "action-buttons",
        components: {Eliminar, Editar},
        props: ['value'],
    }
</script>

<style scoped>

</style>