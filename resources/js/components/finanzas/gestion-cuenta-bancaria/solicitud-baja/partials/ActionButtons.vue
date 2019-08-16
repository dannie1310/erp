<template>
    <div class="btn-group">
        <SolicitudBajaShow v-if="value.show" v-bind:id="value.id" />
        <SolicitudBajaAutorizar v-if="value.show && value.estado == 1" v-bind:id="value.id"/>
    </div>
</template>

<script>
    import SolicitudBajaShow from "../Show";
    import SolicitudBajaAutorizar from "../Autorizar";
    export default {
        name: "action-buttons",
        components: {SolicitudBajaShow, SolicitudBajaAutorizar},
        props: ['value']
    }
</script>