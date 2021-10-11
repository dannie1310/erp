<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header" style="text-align: center">
                <b>SISTEMA CONTROL DE OBRA</b>
                <hr style="border-color: #9e9e9e; margin-bottom: 3px">
            </li>

            <li class="nav-item" v-if="$root.can('consultar_avance_obra')">
                <router-link :to="{name: 'avance-obra'}" class="nav-link" :class="{active: this.$route.name == 'avance-obra'}">
                    <i class="fa fa-circle nav-icon"></i>
                    <p>Avance de Obra</p>
                </router-link>
            </li>
         </ul>
     </nav>
     <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "control-obra-menu",
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
