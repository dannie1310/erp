<template>
    <div class="btn-group">
        <solicitud-movimiento-fondo-garantia-show :id="value.id" v-if="value.show"></solicitud-movimiento-fondo-garantia-show>
        <button @click="autorizar"  v-if="value.autorizar" type="button" class="btn btn-sm btn-outline-success" title="Autorizar"><i class="fa fa-thumbs-up"></i></button>
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
                    .then(() => {
                        this.$emit('success')
                    });
            },
            omitir(){
                return this.$store.dispatch('contabilidad/poliza/omitir', {
                    id: this.poliza.id,
                    params: { include: 'transaccionAntecedente,movimientos,traspaso' }
                })
                    .then(() => {
                        this.$emit('success')
                    })
            }
        }
    }
</script>