<template>
    <div class="btn-group">
        <ComunicadoProveedoresREP v-bind:id="value.id"></ComunicadoProveedoresREP>
        <router-link  :to="{ name: 'envio-comunicado-rep-faltantes', params: {id: value.id}}"  type="button" class="btn btn-sm btn-outline-primary" title="Consultar">
            <i class="fa fa-envelope"></i>
        </router-link>
    </div>
</template>

<script>

    import ComunicadoProveedoresREP from "../ComunicadoProveedoresREP";
    export default {
        name: "ActionButtonsPorProveedor",
        components: {ComunicadoProveedoresREP},
        props: ['value']
    }
</script>

<style scoped>

</style>
