<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>

            <li class="nav-item" v-if="$root.can('consultar_requisicion_compra')">
                <router-link :to="{name: 'requisicion'}" class="nav-link">
                    <i class="fa fa-circle nav-icon"></i>
                    <p>Requisiciones</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_solicitud_compra')">
                <router-link :to="{name: 'solicitud-compra'}" class="nav-link">
                    <i class="fa fa-circle nav-icon"></i>
                    <p>Solicitudes</p>
            <!--<li class="nav-item" v-if="$root.can('consultar_solicitud_compra')">-->
                <!--<router-link :to="{name: 'solicitud-compra'}" class="nav-link">-->
                    <!--<i class="fa fa-circle nav-icon"></i>-->
                    <!--<p>Solicitudes de Compra</p>-->
                <!--</router-link>-->
            <!--</li>-->
            <li class="nav-item">
                <router-link :to="{name: 'cotizacion'}" class="nav-link">
                    <i class="fa fa-circle nav-icon"></i>
                    <p>Cotizaciones</p>
                </router-link>
            </li>
            <!--<li class="nav-item" v-if="$root.can('consultar_banco')">-->
                <!--<router-link :to="{name: 'cotizacion'}" class="nav-link">-->
                    <!--<i class="fa fa-circle nav-icon"></i>-->
                    <!--<p>Cotizaciones</p>-->
                <!--</router-link>-->
            <!--</li>-->
            <!--<li class="nav-item" v-if="$root.can('consultar_banco')">-->
                <!--<router-link :to="{name: 'asignacion-proveedores'}" class="nav-link">-->
                    <!--<i class="fa fa-circle nav-icon"></i>-->
                    <!--<p>Asignación de Proveedores</p>-->
                <!--</router-link>-->
            <!--</li>-->
            <li class="nav-item" v-if="gestion_orden_compra">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-circle"></i>
                    <p>
                        Gestión de OC
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_orden_compra')">
                        <router-link :to="{name: 'orden-compra'}" class="nav-link" :class="{active: this.$route.name == 'orden-compra'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Ordenes de Compra</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item" v-if="catalogo">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-circle"></i>
                    <p>
                        Catálogos
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview"  v-if="$root.can('consultar_familia_material') || $root.can('consultar_familia_herramienta_equipo')">
                    <li class="nav-item" >
                        <router-link :to="{name: 'familia'}" class="nav-link" :class="{active: this.$route.name == 'familia'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Familias</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview" v-if="$root.can('consultar_insumo_material')">
                    <li class="nav-item" >
                        <router-link :to="{name: 'material'}" class="nav-link" :class="{active: this.$route.name == 'material'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Materiales</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview" v-if="$root.can('consultar_insumo_herramienta_equipo')">
                    <li class="nav-item" >
                        <router-link :to="{name: 'herramienta'}" class="nav-link" :class="{active: this.$route.name == 'herramienta'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Herramientas y Equipo</p>
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
        name: "compras-menu",

        computed: {

            catalogo(){
                return this.$root.can([
                    'consultar_familia_material',
                    'consultar_familia_herramienta_equipo',
                    'consultar_insumo_material',
                    'consultar_insumo_herramienta_equipo'
                ]);
            },
            gestion_almacen() {
                return this.$root.can([
                    'consultar_entrada_almacen',
                    'consultar_salida_almacen'
                ])
            },
            gestion_orden_compra(){
                return this.$root.can([
                    'consultar_orden_compra'
                ])
            },
            gestion_solicitud(){
                return this.$root.can([
                    'consultar_solicitud_compra'
                ])
            },
            gestion_asignacion(){
                return this.$root.can([
                    'consultar_solicitud_compra'
                ])
            }
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
