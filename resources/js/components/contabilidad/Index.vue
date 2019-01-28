<template>
    <span>
        <poliza-graph-semanal></poliza-graph-semanal>
        <cuentas-graph-progress></cuentas-graph-progress>
    </span>
</template>

<script>
    import PolizaGraphSemanal from "./poliza/partials/GraphSemanal";
    import CuentasGraphProgress from "./partials/CuentasGraphProgress";
    export default {
        name: "contabilidad-index",
        components: {CuentasGraphProgress, PolizaGraphSemanal}
    }
</script>