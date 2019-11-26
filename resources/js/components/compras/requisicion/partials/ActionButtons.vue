<template>
    <div class="btn-group">
        <Show v-if="value.show" v-bind:id="value.id"/>
    </div>
</template>

<script>
    import Show from '../Show'
    export default {
        name: "ActionButtons",
        components: { Show},
        props: ['value'],
    }
</script>

<style scoped>

</style>