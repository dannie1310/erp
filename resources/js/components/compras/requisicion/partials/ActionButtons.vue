<template>
    <div class="btn-group">
        <Show v-if="value.show" v-bind:id="value.id" />
        <PDF v-if="value.show" v-bind:id="value.id" />
    </div>
</template>

<script>
    import Show from '../Show'
    import PDF from '../FormatoRequisicion'
    export default {
        name: "ActionButtons",
        components: {Show, PDF},
        props: ['value'],
    }
</script>

<style scoped>

</style>