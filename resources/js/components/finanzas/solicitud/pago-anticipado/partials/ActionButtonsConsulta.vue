<template>
    <div class="btn-group">
        <SolicitudPagoAnticipadoShow v-bind:id="value.id" v-bind:numero_folio="value.numero_folio" v-if="$root.can('consultar_solicitud_pago_anticipado')" />
        <PDF  v-if="value.id" v-bind:id="value.id" @click="value.id"></PDF>
        <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'" v-if="$root.can('consultar_solicitud_pago_anticipado') && $root.can('consultar_archivos_transaccion')"></ModalArchivos>
    </div>
</template>

<script>
    import SolicitudPagoAnticipadoShow from "../ShowModal";
    import PDF from './FormatoPagoAnticipado';
    import ModalArchivos from "../../../../globals/archivos/Modal";
    export default {

        name: "action-buttons",
        components: {SolicitudPagoAnticipadoShow, PDF, ModalArchivos},
        props: ['value'],
        methods: {
            cancelar() {
                return this.$store.dispatch('finanzas/solicitud-pago-anticipado/cancel', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    })
            },
            show() {
                this.$router.push({name: 'solicitud-pago-anticipado-show', params: {id: this.value.id}});
            },

        },
    }
</script>
