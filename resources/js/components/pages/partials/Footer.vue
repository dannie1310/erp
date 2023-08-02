<template>
    <footer class="main-footer">

        {{ currentObra && currentObra.descripcion !== undefined ? currentObra.descripcion +' | ' : '' }}

        {{ currentObra && currentObra.facturar !== undefined ? currentObra.facturar +' | ' : '' }}

        {{ !currentObra && currentEmpresa && currentEmpresa.Descripcion !=='null'
    && currentEmpresa.Descripcion !== undefined
        ? currentEmpresa.Descripcion +' | ' :''}}

        {{ currentEmpresa && currentEmpresa.Nombre !=='null'
    && currentEmpresa.Nombre !== undefined
        ? currentEmpresa.Nombre :''}}

    </footer>
</template>

<script>
    export default {
        name: "app-footer",
        computed: {
            currentObra() {
                return this.$store.getters['auth/currentObra']
            },
            currentEmpresa(){
                return this.$store.getters['auth/currentEmpresa']
            },
        }
    }
</script>
