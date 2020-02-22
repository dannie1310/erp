<template>
    <span>
        <div class="btn-group">
            <Eliminar  v-bind:servicio="value.id" v-bind:pagina="value.pagina" v-bind:borrar="value.borrar"></Eliminar>
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