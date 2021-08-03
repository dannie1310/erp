<template>
    <span>
        <invitacion-compra-encabezado v-bind:invitacion="invitacion"></invitacion-compra-encabezado>
        <invitacion-compra-tabla-completa-datos v-bind:invitacion="invitacion"></invitacion-compra-tabla-completa-datos>
    </span>
</template>

<script>

    import InvitacionCompraEncabezado from "../../../compras/invitacion/partials/Encabezado";
    import InvitacionCompraTablaCompletaDatos from "../../../compras/invitacion/partials/TablaCompletaDatosInvitacion";
    export default {
        name: "DatosInvitacion",
        components: {
            InvitacionCompraTablaCompletaDatos,
            InvitacionCompraEncabezado
        },
        props: ['invitacion'],
    }
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}

</style>
