<template>
    <div class="btn-group">
        <SolicitudPagoAnticipadoShow v-if="value.show" v-bind:id="value.id" />
        <SolicitudPagoAnticipadoEdit v-if="value.edit" v-bind:id="value.id" />
        <button @click="pdf" v-if="value.pdf" type="button" class="btn btn-sm btn-outline-primary" title="Descargar Formato PDF"><i class="fa fa-file-pdf-o"></i> </button>
        <button @click="cancelar"  v-if="value.cancelar && value.estado === 0" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar"><i class="fa fa-ban"></i></button>
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
                return this.$store.dispatch('finanzas/solicitud-pago-anticipado/cancel', {id: this.value.id})
                    // .then(() => {
                    //     this.$store.commit('finanzas/solicitud-pago-anticipado/SET_SOLICITUD', this.value);
                    // })
                    .then(() => {
                        this.$emit('success')
                    })
            },
            destroy() {

            },
            show() {
                this.$router.push({name: 'solicitud-pago-anticipado-show', params: {id: this.value.id}});
            },
            pdf(){
                return this.$store.dispatch('finanzas/solicitud-pago-anticipado/pdf', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    })
            }

        },
        mounted() {

        }
    }
</script>