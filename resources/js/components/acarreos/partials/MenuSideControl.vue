<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-justified control-sidebar-menu flex-column"  role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header" style="text-align: center;">
                <hr style="margin: 0px">
                <router-link :to="{name: 'acarreos'}" class="nav-link" :class="{active: this.$route.name == 'acarreos'}">
                    <b>SISTEMA DE ACARREOS</b>
                </router-link>
                <hr style="margin: 0px">
            </li>

            <li class="nav-item" v-if="$root.can('consultar_camion')" style="text-align: left">
                <router-link :to="{name: 'camion'}" class="nav-link" :class="{active: this.$route.name == 'camion'}">
                    <i class="fa fa-truck nav-icon"></i>
                    Camiones
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_empresa')" style="text-align: left">
                <router-link :to="{name: 'empresa-acarreo'}" class="nav-link" :class="{active: this.$route.name == 'empresa-acarreo'}">
                    <i class="fa fa-building nav-icon"></i>
                    Empresas
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_impresora')" style="text-align: left">
                <router-link :to="{name: 'impresora'}" class="nav-link" :class="{active: this.$route.name == 'impresora'}">
                    <i class="fa fa-print nav-icon"></i>
                    Impresoras
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_marca')" style="text-align: left">
                <router-link :to="{name: 'marca'}" class="nav-link" :class="{active: this.$route.name == 'marca'}">
                    <i class="fa fa-th-list nav-icon"></i>
                    Marcas
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_material')" style="text-align: left">
                <router-link :to="{name: 'materiales'}" class="nav-link" :class="{active: this.$route.name == 'materiales'}">
                    <i class="fa fa-cubes nav-icon"></i>
                    Materiales
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_operador')" style="text-align: left">
                <router-link :to="{name: 'operador'}" class="nav-link" :class="{active: this.$route.name == 'operador'}">
                    <i class="fa fa-users nav-icon"></i>
                    Operadores
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_origen')" style="text-align: left">
                <router-link :to="{name: 'origen'}" class="nav-link" :class="{active: this.$route.name == 'origen'}">
                    <i class="fa fa-sign-out-alt nav-icon"></i>
                    Orígenes
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_tiro')" style="text-align: left">
                <router-link :to="{name: 'tiro'}" class="nav-link" :class="{active: this.$route.name == 'tiro'}">
                    <i class="fa fa-sign-in-alt nav-icon"></i>
                    Tiros
                </router-link>
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
                    'consultar_operador',
                    'consultar_origen',
                    'consultar_camion',
                    'consultar_material',
                    'consultar_marca',
                    'consultar_camion',
                    'consultar_impresora'
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
