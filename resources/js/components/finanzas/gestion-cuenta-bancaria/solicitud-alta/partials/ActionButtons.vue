<template>
    <div class="btn-group">
        <SolicitudAltaShow v-if="value.show" v-bind:id="value.id" />
        <button @click="autorizar" v-if="value.autorizar && value.estado == 1" type="button" class="btn btn-sm btn-outline-success" title="Autorizar"><i class="fa fa-check"></i></button>

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
            },
            autorizar() {
                return this.$store.dispatch('finanzas/solicitud-alta-cuenta-bancaria/autorizar', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    })
            },
        },
    }
</script>