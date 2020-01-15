<template>
    <div class="btn-group">
<!--        <Edit v-if="value.edit" v-bind:id="value.id"/>-->
        <Show v-if="value.show" v-bind:id="value.id"/>
<!--        <Delete v-if="value.delete" v-bind:id="value.id"/>-->
    </div>
</template>

<script>
    import Show from "../Show";
    export default {
        name: "ActionButtons",
        components: {Show},
        props: ['value']
    }
</script>

<style scoped>

</style>
