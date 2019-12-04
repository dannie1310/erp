<template>
    <div class="btn-group">
        <Show v-if="value.show" v-bind:id="value.id" />
        <Delete v-if="value.delete" v-bind:id="value.id" v-bind:pagina="value.pagina"/>
        <PDF v-if="value.show" v-bind:id="value.id" />
    </div>
</template>

<script>
    import Show from '../Show'
    import Delete from "../Delete";
    import PDF from '../FormatoRequisicion'
    export default {
        name: "ActionButtons",
        components: {Show, Delete, PDF},
        props: ['value'],
    }
</script>

<style scoped>

</style>