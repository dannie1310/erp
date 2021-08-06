<template>
    <span>
        <encabezado-cotizacion-compra v-bind:cotizacion_compra="cotizacion_compra"></encabezado-cotizacion-compra>
        <tabla-datos-cotizacion-compra v-bind:cotizacion_compra="cotizacion_compra"></tabla-datos-cotizacion-compra>
    </span>

</template>

<script>
import EncabezadoCotizacionCompra from "./EncabezadoCotizacion";
import TablaDatosCotizacionCompra from "./TablaDatosCotizacionCompra";
export default {
    name: "DatosCotizacionCompra",
    components: {
        TablaDatosCotizacionCompra,
        EncabezadoCotizacionCompra
    },
    props: ['cotizacion_compra'],
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
