<template>
    <div class="btn-group">
        <Edit v-if="value.edit" v-bind:id="value.id"/>
    </div>
</template>

<script>
    import Edit from "../Edit";
    export default {
        name: "ActionButtons",
        components: {Edit},
        props: ['value'],
        methods: {
        }
    }
</script>

<style scoped>

</style>
