<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>


            <li class="nav-item" v-if="$root.can(['consultar_solicitud_compra'])">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <p>
                        Gestión de Solicitudes
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" >
                        <router-link :to="{name: 'solicitud-compra'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-compra'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Solicitudes</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <p>
                        Gestión de OC
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_orden_compra')">
                        <router-link :to="{name: 'orden-compra'}" class="nav-link" :class="{active: this.$route.name == 'orden-compra'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Ordenes de Compra</p>
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
        name: "compras-menu",

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