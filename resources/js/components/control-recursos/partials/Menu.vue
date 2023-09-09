<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header" style="text-align: center">
                <b>CONTROL DE RECURSOS</b>
                <hr style="border-color: #9e9e9e; margin-bottom: 3px">
            </li>
            <li class="nav-item">
                <router-link :to="{name: 'documento-recurso'}" v-if="$root.can('consultar_documento_recursos',true)" class="nav-link" :class="{active: this.$route.name == 'documento-recurso'}">
                    <i class="nav-icon fa fa-file-invoice"></i>
                    <p>Documento</p>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link :to="{name: 'factura-recurso'}" v-if="$root.can('consultar_factura_recursos',true)" class="nav-link" :class="{active: this.$route.name == 'factura-recurso'}">
                    <i class="nav-icon fa fa-file-invoice"></i>
                    <p>Factura</p>
                </router-link>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "control-recurso-menu",
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }
    }
</script>



<style scoped>

</style>
