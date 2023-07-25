<template>
    <div class="btn-group">
        <PDFPoliza v-bind:value="value"></PDFPoliza>
    </div>
</template>

<script>
    import PDFPoliza from './PDFPolizaConsulta';
    export default {
        name: "action-buttons",
        components:{PDFPoliza},
        props: ['value'],
    }
</script>

