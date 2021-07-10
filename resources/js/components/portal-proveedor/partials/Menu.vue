<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>
            <li class="nav-item"  v-if="$root.can('consultar_cotizacion_proveedor',true)">
                <router-link :to="{name: 'cotizacion-proveedor'}" class="nav-link">
                    <i class="fa fa-tasks nav-icon"></i>
                    <p>Cotizaciones</p>
                </router-link>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>
<script>
    export default {
        name: "proveedor-menu",
        computed: {

        },
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }

    }
</script>

<style scoped>
    .sidebar-form, .nav-sidebar > .nav-header {
        padding: 1rem 0.5rem 0.5rem 1rem;
    }
</style>
