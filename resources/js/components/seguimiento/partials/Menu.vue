<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">SEGUIMIENTO</li>
            <li class="nav-item"  v-if="$root.can('consultar_factura_cuenta_x_cobrar',true)">
                <router-link :to="{name: 'factura-seg'}" class="nav-link" :class="{active: this.$route.name == 'factura-seg'}">
                    <i class="fa fa-file-invoice-dollar nav-icon"></i>
                    <p>Facturas</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_factura_cuenta_x_cobrar',true)">
                <router-link :to="{name: 'ingreso'}" class="nav-link" :class="{active: this.$route.name == 'ingreso'}">
                    <i class="fa fa-file-invoice-dollar nav-icon"></i>
                    <p>Ingresos</p>
                </router-link>
            </li>
        </ul>

    </nav>
    <!-- /.sidebar-menu -->
</template>
<script>
    export default {
        name: "seguimiento-menu",
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
