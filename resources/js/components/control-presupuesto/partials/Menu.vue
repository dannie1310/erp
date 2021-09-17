<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>
            <li class="nav-item" v-if="$root.can('consultar_presupuesto')">
                <router-link :to="{name: 'presupuesto-obra'}" class="nav-link" :class="{active: this.$route.name == 'presupuesto-obra'}">
                    <i class="fa fa-pencil nav-icon"></i>
                    <p>Editar Conceptos</p>
                </router-link>
            </li>

            <li class="nav-header">SOLICITUDES DE CAMBIO</li>

            <li class="nav-item">
                <router-link :to="{name: 'variacion-volumen'}" class="nav-link"  v-if="$root.can(['consultar_variacion_volumen'])">
                    <i class="nav-icon fa fa-retweet"></i>
                    <p>Aditivas / Deductivas</p>
                </router-link>
            </li>

            <li class="nav-item">
                <router-link :to="{name: 'extraordinario'}" class="nav-link"  v-if="$root.can(['consultar_extraordinario'])">
                    <i class="nav-icon fa fa-plus"></i>
                    <p>Extraordinario</p>
                </router-link>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>
<script>
    export default {
        name: "control-presupuesto-menu",
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
