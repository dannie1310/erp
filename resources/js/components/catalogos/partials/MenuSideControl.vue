<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-justified control-sidebar-menu flex-column"  role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header" style="text-align: center;">
                <hr style="margin: 0px">
                <router-link :to="{name: 'catalogos'}" class="nav-link" :class="{active: this.$route.name == 'catalogos'}">
                    <b>CATÁLOGOS</b>
                </router-link>
                <hr style="margin: 0px">
            </li>
            <li class="nav-item" v-if="catalogo_almacenes" style="text-align: left;">
                <router-link :to="{name: 'almacen'}" class="nav-link" :class="{active: this.$route.name == 'almacen'}">
                    <i class="fa fa-boxes nav-icon"></i>
                    Almacenes
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_unidad')" style="text-align: left;">
                <router-link :to="{name: 'unidad'}" class="nav-link" :class="{active: this.$route.name == 'unidad'}">
                    <i class="fas fa-ruler-combined nav-icon"></i>
                   Unidades
                </router-link>
            </li>
            <li class="nav-item" style="text-align: left;">
                <router-link :to="{name: 'familia-maq'}" class="nav-link" :class="{active: this.$route.name == 'familia-maq'}">
                    <i class="fa fa-tractor nav-icon"></i>
                    Familia de Maquinaria
                </router-link>
            </li>
            <li class="nav-item" style="text-align: left;">
                <router-link :to="{name: 'maquinaria'}" class="nav-link" :class="{active: this.$route.name == 'maquinaria'}">
                    <i class="fa fa-tractor nav-icon"></i>
                    Insumo Maquinaria
                </router-link>
            </li>

            <li class="nav-item" style="text-align: left;">
                <router-link :to="{name: 'cat-familia-serv'}" class="nav-link" :class="{active: this.$route.name == 'cat-familia-serv'}">
                    <i class="fa fa-hand-paper nav-icon"></i>
                    Familia Mano de Obra y Servicios
                </router-link>
            </li>

            <li class="nav-item" style="text-align: left;">
                <router-link :to="{name: 'cat-mano-obra'}" class="nav-link" :class="{active: this.$route.name == 'cat-mano-obra'}">
                    <i class="fa fa-hand-paper nav-icon"></i>
                    Insumo Mano de Obra
                </router-link>
            </li>

            <li class="nav-item" style="text-align: left;">
                <router-link :to="{name: 'cat-servicio'}" class="nav-link" :class="{active: this.$route.name == 'cat-servicio'}">
                    <i class="fa fa-box nav-icon"></i>
                    Insumo Servicio
                </router-link>
            </li>

            <li class="nav-item" style="text-align: left;">
                <router-link :to="{name: 'cat-familia'}" class="nav-link" :class="{active: this.$route.name == 'cat-familia'}">
                    <i class="fa fa-simplybuilt nav-icon"></i>
                    Familia Material, Hta. y Equipo
                </router-link>
            </li>

            <li class="nav-item" style="text-align: left;">
                <router-link :to="{name: 'cat-material'}" class="nav-link" :class="{active: this.$route.name == 'cat-material'}">
                    <i class="fa fa-simplybuilt nav-icon"></i>
                    Insumo Material
                </router-link>
            </li>

            <li class="nav-item" style="text-align: left;">
                <router-link :to="{name: 'cat-herramienta'}" class="nav-link" :class="{active: this.$route.name == 'cat-herramienta'}">
                    <i class="fa fa-tools nav-icon"></i>
                    Insumo Herramienta y Equipo
                </router-link>
            </li>

            <li class="nav-item" style="text-align: left;">
                <router-link :to="{name: 'cliente'}" class="nav-link" :class="{active: this.$route.name == 'cliente'}">
                    <i class="fa fa-users nav-icon"></i>
                    Clientes
                </router-link>
            </li>

            <li class="nav-item" style="text-align: left;">
                <router-link :to="{name: 'destajista'}" class="nav-link" :class="{active: this.$route.name == 'destajista'}">
                    <i class="fa fa-users nav-icon"></i>
                    Destajistas
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_proveedor')" style="text-align: left;">
                <router-link :to="{name: 'proveedor-contratista'}" class="nav-link" :class="{active: this.$route.name == 'proveedor-contratista'}">
                    <i class="fa fa-users nav-icon"></i>
                    Proveedores / Contratistas
                </router-link>
            </li>

            <li class="nav-item"  v-if="$root.can('consultar_unificacion_proveedores')" style="text-align: left;">
                <router-link :to="{name: 'unificacion-proveedores'}" class="nav-link" :class="{active: this.$route.name == 'unificacion-proveedores'}">
                    <i class="fas fa-crosshairs nav-icon"></i>
                    Unificación de Empresas
                </router-link>
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
                    'consultar_insumo_herramienta_equipo',
                    'consultar_familia_material',
                    'consultar_familia_herramienta_equipo'
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
                    'consultar_insumo_servicio',
                    'consultar_familia_mano_obra',
                    'consultar_insumo_mano_obra'
                ]);
            },
            catalogo_almacenes(){
                return this.$root.can([
                    'consultar_almacen_material',
                    'consultar_almacen_maquinaria',
                    'consultar_almacen_maquina_controladora_insumo',
                    'consultar_almacen_mano_obra',
                    'consultar_almacen_servicio',
                    'consultar_almacen_herramienta'
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
