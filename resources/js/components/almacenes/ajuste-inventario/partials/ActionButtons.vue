<template>
    <div class="btn-group">
        <Show v-if="value.show" v-bind:id="value.id" v-bind:pagina="value.pagina" v-bind:tipo="value.tipo"/>
    </div>
</template>

<script>
    import Show from '../Show'
    export default {
        name: "ActionButtons",
        components: {Show},
        props: ['value'],
    }
</script>

<style scoped>

</style>