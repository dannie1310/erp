<template>
    <div class="btn-group">
        <PDF v-bind:id="value.id"></PDF>
    </div>
</template>

<script>
    import PDF from "../FormatoResguardo"
    export default {
        name: "ActionButtons",
        props: ['value'],
        components: { PDF },
        methods: {
        }
    }
</script>

<style scoped>

</style>
