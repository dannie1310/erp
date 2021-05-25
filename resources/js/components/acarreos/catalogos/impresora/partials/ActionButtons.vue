<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-if="value.show" />
    </div>
</template>

<script>
    import Show from "../Show"
    export default {
        name: "ActionButtons",
        props: ['value'],
        components: { Show },
        methods: {
        }
    }
</script>

<style scoped>

</style>
