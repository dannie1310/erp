<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">FONDO DE GARANTÍA</li>
            <li class="nav-item" v-if="$root.can('consultar_cuenta_almacen')">
                <a href="#" class="nav-link" >
                <router-link :to="{name: 'solicitud-movimiento-fg'}" class="nav-link" :class="{active: this.$route.name == 'contratos'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Solicitudes de Movimiento</p>
                </router-link>
                </a>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_cuenta_almacen')">
                <a href="#" class="nav-link" >
                <router-link :to="{name: 'fondo-garantia'}" class="nav-link" :class="{active: this.$route.name == 'contratos'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Consultar</p>
                </router-link>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "fondos-garantia-menu",

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