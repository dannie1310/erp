<template>
    <div class="btn-group">
        <Show  v-bind:id="value.id" v-bind:id_empresa="value.id_empresa" />
        <PDFPoliza v-bind:id="value.id" v-bind:id_empresa="value.id_empresa"></PDFPoliza>
    </div>
</template>

<script>
    import Show from '../../../contabilidad-general/poliza/Show';
    import PDFPoliza from '../../../contabilidad-general/poliza/partials/PDFPoliza';
    export default {
        name: "action-buttons",
        components:{Show, PDFPoliza},
        props: ['value'],
        methods: {

        },
    }
</script>

