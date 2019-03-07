<template>
    <button v-if="$root.can('validar_prepoliza') && (poliza.estatus == -2 || poliza.estatus == 0)" class="btn btn-app btn-info pull-right" @click="validar">
        <i class="fa fa-check-square-o"></i> Validar
    </button>
</template>

<script>
    export default {
        name: "poliza-validar",
        props: ['poliza'],
        methods: {
            validar(){
                return this.$store.dispatch('contabilidad/poliza/validar', {
                    id: this.poliza.id,
                    params: { include: 'transaccionAntecedente,movimientos,traspaso' }
                })
                    .then(data => {
                        this.$store.commit('contabilidad/poliza/UPDATE_POLIZA', data);
                        this.$emit('success')
                    })
            }
        }
    }
</script>