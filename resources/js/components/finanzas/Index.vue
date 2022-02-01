<template>
    <span>
        <pago-anticipado-indicador-aplicados></pago-anticipado-indicador-aplicados>
    </span>
</template>

<script>
    import PagoAnticipadoIndicadorAplicados from "./solicitud/pago-anticipado/partials/IndicadorAplicados";
    export default {
        name: "finanzas-index",
        components: {PagoAnticipadoIndicadorAplicados},
    }
</script>
