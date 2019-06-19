<template>
    <div class="btn-group">
        <solicitud-movimiento-fondo-garantia-show :id="value.id" v-if="value.show"></solicitud-movimiento-fondo-garantia-show>
        <span>
        <button @click="cancelar"  v-if="value.cancelar" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar"><i class="fa fa-ban"></i></button>
        </span>
        <span>
        <button @click="autorizar"  v-if="value.autorizar" type="button" class="btn btn-sm btn-outline-success" title="Autorizar"><i class="fa fa-thumbs-up"></i></button>
        </span>
        <span>
        <button @click="rechazar"  v-if="value.rechazar" type="button" class="btn btn-sm btn-outline-warning" title="Rechazar"><i class="fa fa-thumbs-down"></i></button>
        </span>
        <span>
        <button @click="revertir_autorizacion"  v-if="value.revertir_autorizacion" type="button" class="btn btn-sm btn-outline-danger" title="Revertir Autorización"><i class="fa fa-mail-reply"></i></button>
        </span>
    </div>
</template>

<script>
    import SolicitudMovimientoFondoGarantiaShow from "../Show";
    /*import MovimientoBancarioEdit from "../Edit";*/
    export default {
        name: "action-buttons-smfg",
        components: {SolicitudMovimientoFondoGarantiaShow},
        props: ['value'],
        methods: {
            show() {
                this.$router.push({name: 'solicitud-movimiento-fondo-garantia-show', params: {id: this.value.id}});
            },
            autorizar() {
                return this.$store.dispatch('contratos/solicitud-movimiento-fg/autorizar', {
                    id: this.value.id,
                    /*params: { include: 'transaccionAntecedente,movimientos,traspaso' }*/
                })
                    .then((data) => {
                        this.$store.commit('contratos/solicitud-movimiento-fg/UPDATE_SOLICITUD', data);
                        this.$emit('success')
                    });
            },
            cancelar(){
                return this.$store.dispatch('contratos/solicitud-movimiento-fg/cancelar', {
                    id: this.value.id,
                    /*params: { include: 'transaccionAntecedente,movimientos,traspaso' }*/
                })
                    .then((data) => {
                        this.$store.commit('contratos/solicitud-movimiento-fg/UPDATE_SOLICITUD', data);
                        this.$emit('success')
                    })
            },
            rechazar(){
                return this.$store.dispatch('contratos/solicitud-movimiento-fg/rechazar', {
                    id: this.value.id,
                    /*params: { include: 'transaccionAntecedente,movimientos,traspaso' }*/
                })
                    .then((data) => {
                        this.$store.commit('contratos/solicitud-movimiento-fg/UPDATE_SOLICITUD', data);
                        this.$emit('success')
                    })
            },
            revertir_autorizacion(){
                return this.$store.dispatch('contratos/solicitud-movimiento-fg/revertir_autorizacion', {
                    id: this.value.id,
                    /*params: { include: 'transaccionAntecedente,movimientos,traspaso' }*/
                })
                    .then((data) => {
                        this.$store.commit('contratos/solicitud-movimiento-fg/UPDATE_SOLICITUD', data);
                        this.$emit('success')
                    })
            }
        }
    }
</script>