<template>
    <div class="btn-group">
        <AsociacionCtaProvModal v-bind:id_empresa="value.id_empresa" v-bind:id_cuenta="value.id_cuenta" v-bind:nombre="value.nombre"></AsociacionCtaProvModal>
        <!-- <Show  v-if="$root.can('consultar_salida_almacen')" v-bind:id="value.id" v-bind:pagina="value.pagina" /> -->
    </div>
</template>

<script>
    import AsociacionCtaProvModal from './AsociarCuentaProveedor.vue';
    export default {
        name: "action-buttons",
        components: {AsociacionCtaProvModal},
        props: ['value']
    }

</script>
