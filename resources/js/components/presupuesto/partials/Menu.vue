<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">CONTROL DE PRESUPUESTO</li>


            <li class="nav-item" v-if="$root.can('consultar_presupuesto')">
                <router-link :to="{name: 'concepto'}" class="nav-link" :class="{active: this.$route.name == 'concepto'}">
                    <i class="fa fa-sitemap nav-icon"></i>
                    <p>Presupuesto</p>
                </router-link>
            </li>

         </ul>
     </nav>
     <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "menu-presupuesto",

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
