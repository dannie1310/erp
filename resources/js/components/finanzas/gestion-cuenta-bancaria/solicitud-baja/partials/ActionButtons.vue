<template>
    <div class="btn-group">
        <SolicitudBajaShow v-if="value.show" v-bind:id="value.id" />
    </div>
</template>

<script>
    import SolicitudBajaShow from "../Show";
    export default {
        name: "action-buttons",
        components: {SolicitudBajaShow},
        props: ['value'],
        methods: {
            show() {
                this.$router.push({name: 'solicitud-baja-show', params: {id: this.value.id}});
            }
        },
    }
</script>