<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <poliza-graph-semanal></poliza-graph-semanal>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <cuentas-graph-progress></cuentas-graph-progress>
            </div>
            <div class="col-md-5">
                <poliza-graph-acum></poliza-graph-acum>
            </div>
        </div>
    </span>
</template>

<script>
    import PolizaGraphSemanal from "./poliza/partials/GraphSemanal";
    import CuentasGraphProgress from "./partials/CuentasGraphProgress";
    import PolizaGraphAcum from "./poliza/partials/GraphAcum";
    export default {
        name: "contabilidad-index",
        components: {PolizaGraphAcum, CuentasGraphProgress, PolizaGraphSemanal}
    }
</script>