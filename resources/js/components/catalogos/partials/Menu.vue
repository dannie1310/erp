<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">CATÁLOGOS</li>

            <li class="nav-item" v-if="catalogo_maquinaria">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-tractor"></i>
                    <p>
                        Catálogo de Maquinaria
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
            <li class="nav-item" v-if="catalogo_servicios">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-cogs"></i>
                    <p>
                        Catálogo de Servicios
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview" v-if="$root.can('consultar_familia_servicio')">
                    <li class="nav-item" >
                        <router-link :to="{name: 'cat-familia-serv'}" class="nav-link" :class="{active: this.$route.name == 'cat-familia-serv'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Familia de Servicios</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview" v-if="$root.can('consultar_insumo_servicio')">
                    <li class="nav-item" >
                        <router-link :to="{name: 'cat-servicio'}" class="nav-link" :class="{active: this.$route.name == 'cat-servicio'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Insumo Servicios</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item" v-if="catalogo_insumo">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-server"></i>
                    <p>
                        Catálogo de Insumos
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" >
                        <router-link :to="{name: 'cat-familia'}" class="nav-link" :class="{active: this.$route.name == 'cat-familia'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Familia</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview" v-if="$root.can('consultar_insumo_material')">
                    <li class="nav-item" >
                        <router-link :to="{name: 'cat-material'}" class="nav-link" :class="{active: this.$route.name == 'cat-material'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Material</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview" v-if="$root.can('consultar_insumo_herramienta_equipo')">
                    <li class="nav-item" >
                        <router-link :to="{name: 'cat-herramienta'}" class="nav-link" :class="{active: this.$route.name == 'cat-herramienta'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Herramienta y Equipo</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item" v-if="catalogo_empresa">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-server"></i>
                    <p>
                        Catálogo de Empresas
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" v-if="$root.can('consultar_cliente')">
                    <li class="nav-item" >
                        <router-link :to="{name: 'cliente'}" class="nav-link" :class="{active: this.$route.name == 'cliente'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cliente</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview" v-if="$root.can('consultar_destajista')">
                    <li class="nav-item" >
                        <router-link :to="{name: 'destajista'}" class="nav-link" :class="{active: this.$route.name == 'destajista'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Destajista</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_proveedor')">
                        <router-link :to="{name: 'proveedor-contratista'}" class="nav-link" :class="{active: this.$route.name == 'proveedor-contratista'}">
                            <i class="fa fa-circle nav-icon"></i>
                            <p>Proveedor / Contratista</p>
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
            catalogo_empresa(){
                return this.$root.can([
                   'consultar_cliente',
                    'consultar_destajista',
                    'consultar_proveedor'
                ]);
            },
            catalogo_insumo(){
                return this.$root.can([
                    'consultar_insumo_material',
                    'consultar_insumo_herramienta_equipo'
                ]);
            },
            catalogo_maquinaria(){
                return this.$root.can([
                    'consultar_familia_maquinaria',
                    'consultar_insumo_maquinaria'
                ]);
            },
            catalogo_servicios(){
                return this.$root.can([
                    'consultar_familia_servicio',
                    'consultar_insumo_servicio'
                ]);
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
