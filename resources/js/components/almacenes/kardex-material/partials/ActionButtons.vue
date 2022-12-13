<template>
    <div class="btn-group">
        <Show v-bind:id="value.id"  v-bind:pagina="value.pagina"/>
    </div>
</template>

<script>
    import Show from "../Show";
    export default {
        name: "ActionButtons",
        components: { Show},
        props: ['value']
    }
</script>

<style scoped>

</style>
