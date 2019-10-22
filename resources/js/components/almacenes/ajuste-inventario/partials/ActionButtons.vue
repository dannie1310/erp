<template>
    <div class="btn-group">
        <Show v-if="value.show" v-bind:id="value.id" v-bind:pagina="value.pagina" v-bind:tipo="value.tipo"/>
        <Delete  v-bind:id="value.id" v-bind:pagina="value.pagina" v-bind:tipo="value.tipo"  v-if="value.tipo==0 && $root.can('eliminar_ajuste_positivo') && value.show" />
        <Delete  v-bind:id="value.id" v-bind:pagina="value.pagina" v-bind:tipo="value.tipo"  v-if="value.tipo==1 && $root.can('eliminar_ajuste_negativo') && value.show" />
        <Delete  v-bind:id="value.id" v-bind:pagina="value.pagina" v-bind:tipo="value.tipo"  v-if="value.tipo==2 && $root.can('eliminar_nuevo_lote') && value.show" />
    </div>
</template>

<script>
    import Delete from '../Delete'
    import Show from '../Show'
    export default {
        name: "ActionButtons",
        components: {Delete, Show},
        props: ['value'],
    }
</script>

<style scoped>

</style>
