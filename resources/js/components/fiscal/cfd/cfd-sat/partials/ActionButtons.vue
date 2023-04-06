<template>
    <div class="btn-group">
        <CFDI v-bind:id="value.id" @click="value.id" ></CFDI>
        <DescargaCFDI v-bind:id="value.id"></DescargaCFDI>
        <ValidarVigenciaCFDI v-bind:id="value.id"  ></ValidarVigenciaCFDI>
    </div>
</template>

<script>
    import CFDI from "../CFDI";
    import DescargaCFDI from "../DescargaCFDI";
    import ValidarVigenciaCFDI from "./ValidaVigenciaCFDI.vue";
    export default {
        name: "ActionButtons",
        components: {ValidarVigenciaCFDI, DescargaCFDI, CFDI},
        props: ['value'],

    }
</script>

<style scoped>

</style>
