<template>
    <span>
        <div class="btn-group">
            <Eliminar  v-if="$root.can('eliminar_factura')" v-bind:id="value.id" v-bind:pagina="value.pagina" v-bind:borrar="value.borrar"></Eliminar>
        </div>
    </span>
</template>

<script>
    import Eliminar from '../Eliminar';
    export default {
        name: "action-buttons",
        components: {Eliminar},
        props: ['value'],
    }
</script>

<style scoped>

</style>