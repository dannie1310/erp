<template>
    <div class="btn-group">
        <SolicitudBajaShow v-if="value.show" v-bind:id="value.id" />
        <SolicitudBajaCancelar v-if="value.cancelar && value.mov_estado == 1" v-bind:id="value.id"/>
    </div>
</template>

<script>
    import SolicitudBajaShow from "../Show";
    import SolicitudBajaCancelar from "../Cancelar";
    export default {
        name: "action-buttons",
        components: {SolicitudBajaShow, SolicitudBajaCancelar},
        props: ['value']
    }
</script>