<template>
    <div class="btn-group">
        <CFDI v-bind:id="value.id" @click="value.id" ></CFDI>
    </div>
</template>

<script>
    import CFDI from "../../../fiscal/cfd/cfd-sat/CFDI";
    export default {
        name: "ActionButtons",
        components: {CFDI},
        props: ['value']
    }
</script>

<style scoped>

</style>
