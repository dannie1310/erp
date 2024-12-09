<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">NÓMINAS</li>
            <li class="nav-item"  v-if="$root.can('consultar_poliza_nominas_ctpq',true)">
                <router-link :to="{name: 'seleccionar-empresa-nom'}" class="nav-link" :class="{active: this.$route.name == 'seleccionar-empresa-nom'}">
                    <i class="fa fa-file-invoice-dollar nav-icon"></i>
                    <p>Polizas de Nómina</p>
                </router-link>
            </li>
        </ul>

    </nav>
    <!-- /.sidebar-menu -->
</template>
<script>
    export default {
        name: "nomina-menu",
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
