<template>
    <cancelar v-if="$root.can('eliminar_conteos') && value.estado == 0" v-bind:id="value.id" v-bind:pagina="value.pagina"></cancelar>
</template>

<script>
    import Layout from "../../conteo/CargarLayout";
    import Cancelar from "../../conteo/Cancelar";
    export default {
        name: "action-buttons",
        components: {Layout, Cancelar},
        props: ['value'],
        data() {
            return {
                cargando: false
            }
        }
    }

</script>
