<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'poliza-contpaq-cfdi-show', params: {id: value.id, id_empresa: value.id_empresa}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <PDFPoliza v-bind:id="value.id" v-bind:id_empresa="value.id_empresa"></PDFPoliza>
    </div>
</template>

<script>
    import Show from '../../poliza/Show';
    import PDFPoliza from '../../poliza/partials/PDFPoliza';
    export default {
        name: "action-buttons",
        components:{Show, PDFPoliza},
        props: ['value'],
        methods: {

        },
    }
</script>

