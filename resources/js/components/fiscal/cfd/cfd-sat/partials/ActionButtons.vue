<template>
    <div class="btn-group">
        <CFDI v-bind:id="value.id" @click="value.id" ></CFDI>
    </div>
</template>

<script>
    import CFDI from "../CFDI";
    export default {
        name: "ActionButtons",
        components: {CFDI},
        props: ['value']
    }
</script>

<style scoped>

</style>
