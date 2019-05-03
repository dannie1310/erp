<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>
            <li class="nav-item">
                <router-link :to="{name: 'movimiento-bancario'}" class="nav-link">
                    <i class="fa fa-file-text nav-icon"></i>
                    <p>Movimientos Bancarios</p>
                </router-link>
            </li>

            <li class="nav-item">
                <router-link :to="{name: 'traspaso-entre-cuentas'}" class="nav-link">
                    <i class="fa fa-file-text nav-icon"></i>
                    <p>Traspasos Entre Cuentas</p>
                </router-link>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "tesoreria-menu",
    }
</script>

<style scoped>
    .sidebar-form, .nav-sidebar > .nav-header {
        padding: 1rem 0.5rem 0.5rem 1rem;
    }
</style>