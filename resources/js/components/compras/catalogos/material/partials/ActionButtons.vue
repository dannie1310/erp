<template>
    <Edit v-if="value.edit" v-bind:id="value.id" :key="value.id"/>
</template>

<script>
    import Edit from "../Edit"
    export default {
        name: "ActionButtons",
        components: { Edit },
        props: ['value']
    }
</script>

<style scoped>

</style>
