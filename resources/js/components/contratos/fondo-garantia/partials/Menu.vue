<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">FONDO DE GARANTÍA</li>

            <li class="nav-item" v-if="$root.can('consultar_cuenta_almacen')">
                <a href="#" class="nav-link" >
                <router-link :to="{name: 'solicitud-movimiento'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-movimiento'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Solicitar Movimiento</p>
                </router-link>
                </a>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_cuenta_almacen')">
                <a href="#" class="nav-link" >
                <router-link :to="{name: 'ajuste-saldo-fg'}" class="nav-link" :class="{active: this.$route.name == 'ajuste-saldo-fg'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Ajuste de Saldo</p>
                </router-link>
                </a>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_cuenta_almacen')">
                <a href="#" class="nav-link" >
                <router-link :to="{name: 'consultar-fg'}" class="nav-link" :class="{active: this.$route.name == 'consultar-fg'}">
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