<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'solicitud-pago-anticipado-show', params: {id: value.id}}" v-if="$root.can('consultar_solicitud_pago_anticipado')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </router-link>
        <PDF  v-if="value.id" v-bind:id="value.id" @click="value.id"></PDF>
        <button @click="cancelar"  v-if="value.cancelar && value.estado === 0" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar"><i class="fa fa-ban"></i></button>
        <Relaciones v-bind:transaccion="value.transaccion"/>
        <router-link  :to="{ name: 'solicitud-pago-anticipado-documentos', params: {id: value.id}}" v-if="$root.can('consultar_solicitud_pago_anticipado') && $root.can('consultar_archivos_transaccion')" type="button" class="btn btn-sm btn-outline-primary" title="Ver Archivos">
            <i class="fa fa-folder-open"></i>
        </router-link>
    </div>
</template>

<script>
    import SolicitudPagoAnticipadoCreate from "../Create";
    import PDF from './FormatoPagoAnticipado';
    import Relaciones from "../../../../globals/ModalRelaciones";
    export default {

        name: "action-buttons",
        components: {SolicitudPagoAnticipadoCreate, PDF, Relaciones},
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
