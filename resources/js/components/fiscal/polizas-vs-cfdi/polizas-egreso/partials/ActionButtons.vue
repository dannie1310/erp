<template>
    <div class="btn-group">
        <PDFPoliza v-bind:id="value.id" v-bind:id_empresa="value.id_empresa" v-bind:txt_btn="''"></PDFPoliza>
        <router-link  :to="{ name: 'show-poliza-egreso-sin-cfdi', params: {id: value.id, id_empresa: value.id_empresa}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
    </div>
</template>

<script>
    import PDFPoliza from '../../../../contabilidad-general/poliza/partials/PDFPoliza';
    import PolizaPartialShow from "../../../../contabilidad-general/poliza/partials/PartialShow.vue";
    export default {
        name: "action-buttons",
        components:{PolizaPartialShow, PDFPoliza},
        props: ['value'],
        methods: {

        },
    }
</script>

