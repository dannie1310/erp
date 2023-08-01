<template>
    <div class="btn-group">
        <PDFPoliza v-bind:value="value"></PDFPoliza>
    </div>
</template>

<script>
    import PDFPoliza from './PDFPolizaConsulta';
    import PDFPoliza from "../../../contabilidad-general/poliza/partials/PDFPolizaConsulta.vue";

    export default {
        name: "action-buttons",
        components:{PDFPoliza},
        props: ['value'],
    }
</script>

