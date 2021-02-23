<template>
    <div class="btn-group">
        <CFDI v-bind:id="value.id" @click="value.id" ></CFDI>
        <DescargaCFDI v-bind:id="value.id"></DescargaCFDI>
    </div>
</template>

<script>
    import CFDI from "../CFDI";
    import DescargaCFDI from "../DescargaCFDI";
    export default {
        name: "ActionButtons",
        components: {DescargaCFDI, CFDI},
        props: ['value']
    }
</script>

<style scoped>

</style>
