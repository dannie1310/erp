<template>
    <footer class="main-footer">

        <div class="float-right d-none d-sm-block" v-if="currentObra">
            <small>{{ currentObra ? currentObra.direccion : '' }}</small>
        </div>
        {{ currentObra ? currentObra.facturar : '' }}
        {{ currentEmpresa && currentEmpresa.Descripcion !=='null'
    && currentEmpresa.Descripcion !== undefined
        ? currentEmpresa.Descripcion :''}} -

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
