<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>
            <li class="nav-item">
                <router-link :to="{name: 'solicitud-cambio'}" class="nav-link">
                    <i class="nav-icon fa fa-circle"></i>
                    <p>Cambios Al Presupuesto </p>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link :to="{name: 'variacion-volumen'}" class="nav-link"  v-if="$root.can(['consultar_variacion_volumen'])">
                    <i class="nav-icon fa fa-circle"></i>
                    <p>Variación de Volumen <br>
                        (Aditivas o Deductivas) </p>
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