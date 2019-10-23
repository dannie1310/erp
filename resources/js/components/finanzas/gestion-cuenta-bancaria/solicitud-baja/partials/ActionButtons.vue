<template>
    <div class="btn-group">
        <SolicitudBajaShow v-if="value.show" v-bind:id="value.id" />
        <SolicitudBajaAutorizar v-if="value.autorizar && value.estado == 1" v-bind:id="value.id"/>
        <SolicitudBajaCancelar v-if="value.cancelar && value.estado == 1" v-bind:id="value.id"/>
        <SolicitudBajaRechazar v-if="value.rechazar && value.estado == 1" v-bind:id="value.id"/>
    </div>
</template>

<script>
    import SolicitudBajaShow from "../Show";
    import SolicitudBajaRechazar from "../rechazar";
    import SolicitudBajaCancelar from "../Cancelar";
    import SolicitudBajaAutorizar from "../Autorizar";
    export default {
        name: "action-buttons",
        components: {SolicitudBajaShow, SolicitudBajaRechazar, SolicitudBajaAutorizar, SolicitudBajaCancelar},
        props: ['value']
    }
</script>