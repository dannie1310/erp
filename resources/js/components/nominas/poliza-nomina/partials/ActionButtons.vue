<template>
    <div class="btn-group">
        <DescargaXML v-if="value.xml_ifs" v-bind:id="value.id" v-bind:id_empresa="value.id_empresa" />
        <button @click="correo" v-if="value.correo_xml_ifs" type="button" class="btn btn-sm btn-outline-success" title="Envio Correo de XML">
            <i class="fa fa-envelope"></i>
        </button>
    </div>
</template>

<script>
    import DescargaXML from "../DescargaXML";
    export default {
        name: "poliza-nomina-action-buttons",
        components: {DescargaXML},
        props: ['value'],
        methods: {
            correo() {
                return this.$store.dispatch('nominas/poliza-contpaq/correo', {
                    id: this.value.id,
                    params: {
                        id_empresa: this.value.id_empresa,
                    }})
                    .then((data) => {
                    })
            },
        }
    }
</script>
