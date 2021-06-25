<template>
    <div class="btn-group">
        <Show v-if="value.show" v-bind:id="value.id" />
        <Edit v-if="value.edit" v-bind:id="value.id"/>
        <Delete v-if="value.delete" v-bind:id="value.id" />
    </div>
</template>

<script>
    import Edit from "../Edit";
    import Delete from "../Delete";
    import Show from "../Show";
    export default {
        name: "ActionButtons",
        components: {Edit, Delete, Show},
        props: ['value'],
        methods: {
        }
    }
</script>

<style scoped>

</style>
