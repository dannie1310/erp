<template>
    <div class="btn-group">
        <Edit v-if="value.edit" v-bind:id="value.id"/>
        <Show v-if="value.show" v-bind:id="value.id"/>
    </div>
</template>

<script>
    import Edit from "../Edit";
    import Show from "../Show";
    export default {
        name: "ActionButtons",
        components: {Edit, Show},
        props: ['value'],
        methods: {

        }
    }
</script>

<style scoped>

</style>
