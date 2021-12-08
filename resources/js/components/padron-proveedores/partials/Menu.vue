<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>
            <li class="nav-item"  v-if="$root.can('consultar_expediente_proveedor',true)" >
                <router-link :to="{name: 'proveedores-index'}" class="nav-link" :class="{active: this.$route.name == 'proveedores-index'}">
                    <i class="fa fa-users nav-icon"></i>
                    <p>Proveedores</p>
                </router-link>
            </li>
            <li class="nav-item" >
                <router-link :to="{name: 'empresas-boletinadas-index'}" class="nav-link" :class="{active: this.$route.name == 'empresas-boletinadas-index'}">
                    <i class="fa fa-exclamation-triangle nav-icon"></i>
                    <p>Empresas Boletinadas</p>
                </router-link>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>
<script>
    export default {
        name: "padron-proveedores-menu",
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
