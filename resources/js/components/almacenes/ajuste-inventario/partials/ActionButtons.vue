<template>
    <div class="btn-group">
        <Show v-if="value.show" v-bind:id="value.id" v-bind:pagina="value.pagina" v-bind:tipo="value.tipo"/>
        <Delete v-if="value.show" v-bind:id="value.id" v-bind:pagina="value.pagina" v-bind:tipo="value.tipo" />
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
