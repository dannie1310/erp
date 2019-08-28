<template>
    <div class="btn-group">
    <Delete v-if="value.delete && value.estado == 0" v-bind:id="value.id" v-bind:pagina="value.pagina"/>
    </div>
</template>

<script>
    import Delete from "../Delete";
    export default {
        name: "ActionButtons",
        components: {Delete},
        props: ['value']
    }
</script>

<style scoped>

</style>