<template>
    <div class="btn-group">
        <SolicitudAltaShow v-if="value.show" v-bind:id="value.id" />
    </div>
</template>

<script>
    import SolicitudAltaShow from "../Show";
    export default {

        name: "action-buttons",
        components: {SolicitudAltaShow},
        props: ['value'],
        methods: {
            show() {
                this.$router.push({name: 'solicitud-pago-anticipado-show', params: {id: this.value.id}});
            }
        },
    }
</script>