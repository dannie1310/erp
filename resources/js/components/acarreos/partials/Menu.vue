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
                    <li class="nav-item" v-if="$root.can('consultar_tiro')">
                        <router-link :to="{name: 'origen'}" class="nav-link" :class="{active: this.$route.name == 'origen'}">
                            <i class="fa fa-layer-group"></i>
                            <p> Origenes</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_tiro')">
                        <router-link :to="{name: 'tiro'}" class="nav-link" :class="{active: this.$route.name == 'tiro'}">
                            <i class="fa fa-layer-group"></i>
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
                    'consultar_tiro'
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
