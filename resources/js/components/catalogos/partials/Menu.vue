<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">CATALOGOS</li>

            <li class="nav-item" v-if="$root.can('consultar_familia_maquinaria')|| $root.can('consultar_insumo_maquinaria')">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-tractor"></i>
                    <p>
                        Catalogo de Maquinaria
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview" v-if="$root.can('consultar_familia_maquinaria')">
                    <li class="nav-item" >
                        <router-link :to="{name: 'familia-maq'}" class="nav-link" :class="{active: this.$route.name == 'familia-maq'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Familia de Maquinaria</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview" v-if="$root.can('consultar_insumo_maquinaria')">
                    <li class="nav-item" >
                        <router-link :to="{name: 'maquinaria'}" class="nav-link" :class="{active: this.$route.name == 'maquinaria'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Insumo Maquinaria</p>
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
        name: "catalogos-menu",

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
