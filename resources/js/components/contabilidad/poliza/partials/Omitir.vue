<template>
    <button v-if="$root.can('omitir_prepoliza_generada') && (poliza.estatus == -2 || poliza.estatus == -1 || poliza.estatus == 0)" class="btn btn-app btn-info float-right" @click="omitir">
        <i class="fa fa-thumbs-o-down"></i> Omitir
    </button>
</template>

<script>
    export default {
        name: "poliza-omitir",
        props: ['poliza'],
        methods: {
            omitir(){
                return this.$store.dispatch('contabilidad/poliza/omitir', {
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