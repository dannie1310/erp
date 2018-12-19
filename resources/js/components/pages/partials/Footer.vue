<template>
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <small>{{ currentObra ? currentObra.direccion : '' }}</small>
        </div>
        {{ currentObra ? currentObra.facturar : '' }}
    </footer>
</template>

<script>
    export default {
        name: "app-footer",
        computed: {
            currentObra() {
                return this.$store.getters['auth/currentObra']
            }
        }
    }
</script>