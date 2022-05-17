<template>
    <td style="text-align: right; " :style="value!=0 && value != '-'?`text-decoration: underline; cursor: pointer`:value<0?`color : #ff0000`:``"
        v-on:click="verCFDI(partida,tipo)">
        <template v-if="value<0">(</template>
            <span v-if="value!=0 && value != '-'">{{  parseFloat(Math.abs(value)).formatMoney(0,'.',',') }}</span>
            <span v-else>-</span>
        <template v-if="value<0">)</template>
    </td>
</template>

<script>

export default {
    name: "td_informe_link_cfdi",
    props: ['partida', 'tipo', 'value'],
    data() {
        return {
            abriendo_modal: 0,
        }
    },
    methods: {
        verCFDI(partida, tipo = 1)
        {
            this.$emit("ver-cfdi", partida, tipo)
            /*if(this.abriendo_modal == 0) {
                this.abriendo_modal = 1;
                return this.$store.dispatch('fiscal/cfd-sat/getListaCFDIMesAnio', {
                    empresa_sat: this.empresa_sat_seleccionada,
                    mes: partida.id_mes,
                    anio: this.anio_input
                })
                    .then(data => {
                        this.lista_cfdi = data.informe;
                        this.razon_social = partida.razon_social;
                        this.rfc = partida.rfc;
                        this.total_cfdi = data.total;
                    })
                    .finally(() => {
                        $(this.$refs.modal_cfdi).appendTo('body')
                        $(this.$refs.modal_cfdi).modal('show');
                        this.abriendo_modal = 0;
                    });
            }*/
        },
    },
}
</script>

<style scoped>

</style>
