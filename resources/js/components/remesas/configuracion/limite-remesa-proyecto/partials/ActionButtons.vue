<template>
    <div class="btn-group">
        <Edit v-bind:id_proyecto="value.id_proyecto" v-if="value.edit" />
    </div>
</template>

<script>
    import Edit from "../Edit";
    export default {
        name: "limite-remesa-proyecto-actions",
        props: ['value'],
        components: {Edit},
        methods: {

        }
    }
</script>

<style scoped>

</style>
