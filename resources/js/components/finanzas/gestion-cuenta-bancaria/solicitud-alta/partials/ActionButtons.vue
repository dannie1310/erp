<template>
    <div class="btn-group">
        <SolicitudAltaShow v-if="value.show" v-bind:id="value.id" />
        <SolicitudAltaAutorizar v-if="value.autorizar && value.estado == 1" v-bind:id="value.id"/>
        <SolicitudAltaRechazar v-if="value.rechazar && value.estado == 1" v-bind:id="value.id"/>
    </div>
</template>

<script>
    import SolicitudAltaShow from "../Show";
    import SolicitudAltaAutorizar from "../Autorizar";
    import SolicitudAltaRechazar from "../Rechazar";
    export default {

        name: "action-buttons",
        components: {SolicitudAltaShow, SolicitudAltaAutorizar, SolicitudAltaRechazar},
        props: ['value'],
        methods: {
            show() {
                this.$router.push({name: 'solicitud-alta-show', params: {id: this.value.id}});
            }
        },
    }
</script>