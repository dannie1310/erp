<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>
            <li class="nav-item" v-if="$root.can('consultar_inventario_fisico')">
                <router-link :to="{name: 'inventario-fisico'}" class="nav-link">
                    <i class="nav-icon fa fa-server"></i>
                    <p>Inventario Físico</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="ajuste_inventario">
                <router-link :to="{name: 'ajuste-inventario'}" class="nav-link">
                    <i class="nav-icon fa fa-server"></i>
                    <p>Ajuste de Inventario</p>
                </router-link>
            </li>
<!--            <li class="nav-item" v-if="$root.can('consultar_inventario_fisico')">-->
<!--                <router-link :to="{name: 'conteo'}" class="nav-link">-->
<!--                    <i class="nav-icon fa fa-server"></i>-->
<!--                    <p>Conteos</p>-->
<!--                </router-link>-->
<!--            </li>-->
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "almacenes-menu",

        computed: {
            ajuste_inventario() {
                return this.$root.can([
                    'consultar_ajuste_positivo',
                    'consultar_ajuste_negativo'
                ])
            },
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
