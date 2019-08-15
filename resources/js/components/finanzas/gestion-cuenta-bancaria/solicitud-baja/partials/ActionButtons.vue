<template>
    <div class="btn-group">
        <SolicitudBajaShow v-if="value.show" v-bind:id="value.id" />
        <SolicitudBajaRechazar v-if="value.rechazar && value.mov_estado == 1" v-bind:id="value.id"/>
    </div>
</template>

<script>
    import SolicitudBajaShow from "../Show";
    import SolicitudBajaRechazar from "../rechazar";
    export default {
        name: "action-buttons",
        components: {SolicitudBajaShow, SolicitudBajaRechazar},
        props: ['value']
    }
</script>