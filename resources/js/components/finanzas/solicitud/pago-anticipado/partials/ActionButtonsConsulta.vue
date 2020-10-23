<template>
    <div class="btn-group">
        <SolicitudPagoAnticipadoShow v-if="value.show" v-bind:id="value.id" />
        <PDF  v-if="value.id" v-bind:id="value.id" @click="value.id"></PDF>
        <router-link  :to="{ name: 'solicitud-pago-anticipado-documentos', params: {id: value.id}}" v-if="$root.can('consultar_solicitud_pago_anticipado') && $root.can('consultar_archivos_transaccion')" type="button" class="btn btn-sm btn-outline-primary" title="Ver Archivos">
            <i class="fa fa-folder-open"></i>
        </router-link>
    </div>
</template>

<script>
    import SolicitudPagoAnticipadoShow from "../Show";
    import SolicitudPagoAnticipadoEdit from "../Edit";
    import SolicitudPagoAnticipadoCreate from "../Create";
    import PDF from './FormatoPagoAnticipado';
    export default {

        name: "action-buttons",
        components: {SolicitudPagoAnticipadoCreate, SolicitudPagoAnticipadoEdit, SolicitudPagoAnticipadoShow, PDF},
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
            validate(){
                this.$validator.validate().then(result=>{
                    if(result){

                    }
                });
            }

        },
        mounted() {

        }
    }
</script>
