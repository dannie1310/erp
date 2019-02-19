<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>
            <li class="nav-item">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-money"></i>
                    <p>
                        Fondo de Garantía
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_cuenta_almacen')">
                        <router-link :to="{name: 'solicitud-movimiento-fg'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-movimiento-fg'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Solicitar Movimiento</p>
                        </router-link>
                    </li>

                    <li class="nav-item" v-if="$root.can('consultar_cuenta_almacen')">
                        <router-link :to="{name: 'ajuste-saldo-fg'}" class="nav-link" :class="{active: this.$route.name == 'ajuste-saldo-fg'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Ajuste de Saldo</p>
                        </router-link>
                    </li>

                    <li class="nav-item" v-if="$root.can('consultar_cuenta_almacen')">
                        <router-link :to="{name: 'consultar-fg'}" class="nav-link" :class="{active: this.$route.name == 'consultar-fg'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Consultar</p>
                        </router-link>
                    </li>
                </ul>
            </li>


        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "subcontratos-menu",

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