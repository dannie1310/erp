<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">CATÁLOGOS</li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-money"></i>
                    <p>
                        Cuentas Contables
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_cuenta_almacen')">
                        <router-link :to="{name: 'cuenta-almacen'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-almacen'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cuentas de Almacén</p>
                        </router-link>
                    </li>
                    <li class="nav-item" >
                        <router-link :to="{name: 'cuenta-empresa'}" class="nav-link" >
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cuentas de Empresa</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_cuenta_fondo')">
                        <router-link :to="{name: 'cuenta-fondo'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-fondo'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cuentas de Fondo</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_cuenta_general')">
                        <router-link :to="{name: 'cuenta-general'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-general'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cuentas Generales</p>
                        </router-link>
                    </li>
                </ul>
            </li>

            <li class="nav-header">MÓDULOS</li>
            <li class="nav-item">
                <router-link :to="{name: 'poliza'}" class="nav-link">
                    <i class="fa fa-file-text nav-icon"></i>
                    <p>Prepólizas Generadas</p>
                </router-link>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "contabilidad-menu",

        methods: {
            mostrarMenu(event) {
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