<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills control-sidebar-menu flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header" style="text-align: center;">
                <hr style="margin: 0px">
                <router-link :to="{name: 'almacenes'}" class="nav-link" :class="{active: this.$route.name == 'almacenes'}">
                <b>SISTEMA DE ALMACENES</b>
                </router-link>
                <hr style="margin: 0px">
            </li>
            <li class="nav-item" v-if="$root.can('consultar_entrada_almacen')">
                <router-link :to="{name: 'entrada-almacen'}" class="nav-link" :class="{active: this.$route.name == 'entrada-almacen'}">
                    <i class="nav-icon fa fa-sign-in"></i>
                    Entrada de Almacén
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_salida_almacen')">
                <router-link :to="{name: 'salida-almacen'}" class="nav-link" :class="{active: this.$route.name == 'salida-almacen'}">
                    <i class="nav-icon fa fa-sign-out"></i>
                    Salida de Almacén
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_inventario_fisico')">
                <router-link :to="{name: 'inventario-fisico'}" class="nav-link" :class="{active: this.$route.name == 'inventario-fisico'}">
                    <i class="nav-icon fas fa-clipboard-check"></i>
                    Inventario Físico
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_conteos')">
                <router-link :to="{name: 'conteo'}" class="nav-link" :class="{active: this.$route.name == 'conteo'}">
                    <i class="nav-icon fas fa-hand-peace"></i>
                    Conteos
                </router-link>
            </li>
            <li class="nav-item" v-if="ajuste_inventario">
                <router-link :to="{name: 'ajuste-inventario'}" class="nav-link" :class="{active: this.$route.name == 'ajuste-inventario'}">
                    <i class="nav-icon fa fa-random"></i>
                    Ajuste de Inventario
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_marbetes')">
                <router-link :to="{name: 'marbete'}" class="nav-link" :class="{active: this.$route.name == 'marbete'}">
                    <i class="nav-icon fa fa-tags"></i>
                    Marbetes
                </router-link>
            </li>
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
