<template>
    <div class="btn-group">
        <DescargaXML v-if="value.xml_ifs" v-bind:id="value.id" v-bind:bd_empresa="value.bd_empresa" />
    </div>
</template>

<script>
    import DescargaXML from "../DescargaXML";
    export default {
        name: "poliza-nomina-action-buttons",
        components: {DescargaXML},
        props: ['value'],
        methods: {}
    }
</script>
