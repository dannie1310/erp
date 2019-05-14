<template>
    <div class="btn-group">
        <SolicitudPagoAnticipadoShow v-if="value.show" v-bind:id="value.id" />
        <SolicitudPagoAnticipadoEdit v-if="value.edit" v-bind:id="value.id" />
        <button @click="cancelar"  v-if="value.cancelar" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar"><i class="fa fa-ban"></i></button>
    </div>
</template>

<script>
    import SolicitudPagoAnticipadoShow from "../Show";
    import SolicitudPagoAnticipadoEdit from "../Edit";
    import SolicitudPagoAnticipadoCreate from "../Create";
    export default {
        name: "action-buttons",
        components: {SolicitudPagoAnticipadoCreate, SolicitudPagoAnticipadoEdit, SolicitudPagoAnticipadoShow},
        props: ['value'],
        methods: {
            cancelar() {

            },
            destroy() {

            },
        },
        mounted() {
        }
    }
</script>