<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">SISTEMA DE ACARREOS</li>

            <li class="nav-item" v-if="catalogos">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa fa-circle nav-icon"></i>
                    <p>
                        Catálogos
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_camion')">
                        <router-link :to="{name: 'camion'}" class="nav-link" :class="{active: this.$route.name == 'camion'}">
                            <i class="fa fa-layer-group nav-icon"></i>
                            <p> Camiones</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_material')">
                        <router-link :to="{name: 'materiales'}" class="nav-link" :class="{active: this.$route.name == 'materiales'}">
                            <i class="fa fa-layer-group nav-icon"></i>
                            <p> Materiales</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_origen')">
                        <router-link :to="{name: 'origen'}" class="nav-link" :class="{active: this.$route.name == 'origen'}">
                            <i class="fa fa-layer-group nav-icon"></i>
                            <p> Orígenes</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_tiro')">
                        <router-link :to="{name: 'tiro'}" class="nav-link" :class="{active: this.$route.name == 'tiro'}">
                            <i class="fa fa-layer-group nav-icon"></i>
                            <p> Tiros</p>
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
        name: "acarreos-menu",

        computed: {
            catalogos() {
                return this.$root.can([
                    'consultar_tiro',
                    'consultar_origen',
                    'consultar_camion',
                    'consultar_material'
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
