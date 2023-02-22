<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">Concurso</li>
            <li class="nav-item"  v-if="$root.can('consultar_concurso',true)">
                <router-link :to="{name: 'concurso'}" class="nav-link" :class="{active: this.$route.name == 'concurso'}">
                    <i class="fa fa-mail-bulk nav-icon"></i>
                    <p>Concurso</p>
                </router-link>
            </li>
        </ul>

    </nav>
    <!-- /.sidebar-menu -->
</template>
<script>
    export default {
        name: "concursos-menu",
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
