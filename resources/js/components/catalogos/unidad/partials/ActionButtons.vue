<template>
    <span>
        <div class="btn-group">
            <Eliminar  v-if="$root.can('eliminar_unidad')" v-bind:unidad="value.unidad" v-bind:pagina="value.pagina" v-bind:borrar="value.borrar"></Eliminar>
            <Editar  v-if="$root.can('editar_unidad')" v-bind:unidad="value.unidad" v-bind:pagina="value.pagina" v-bind:editar="value.editar"></Editar>
            
        </div>
    </span>
</template>

<script>
    import Eliminar from '../Eliminar';
    import Editar from '../Edit';
    export default {
        name: "action-buttons",
        components: {Eliminar, Editar},
        props: ['value'],
    }
</script>

<style scoped>

</style>