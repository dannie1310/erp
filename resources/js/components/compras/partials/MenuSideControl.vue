<template>
    <span>
    <nav class="mt-2">

          <ul class="nav nav-pills nav-justified control-sidebar-menu flex-column" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header" style="text-align: center;">
                <hr style="margin: 0px">
                <router-link :to="{name: 'compras'}" class="nav-link" :class="{active: this.$route.name == 'compras'}">
                    <b>SISTEMA DE COMPRAS</b>
                </router-link>
                <hr style="margin: 0px">
            </li>

            <li class="nav-item" v-if="$root.can('consultar_requisicion_compra')" style="text-align: left">
                <router-link :to="{name: 'requisicion'}" class="nav-link" :class="{active: this.$route.name == 'requisicion'}">
                    <i class="fa fa-circle nav-icon"></i>
                    Requisiciones
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_solicitud_compra')" style="text-align: left">
                <router-link :to="{name: 'solicitud-compra'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-compra'}">
                    <i class="fa fa-comment-dots nav-icon"></i>
                    Solicitudes
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_invitacion_cotizar_compra')" style="text-align: left">
                <router-link :to="{name: 'invitacion-compra'}" class="nav-link" :class="{active: this.$route.name == 'invitacion-compra'}">
                    <i class="fa fa-envelope-open-text nav-icon"></i>
                    Invitaciones a Cotizar
                </router-link>
            </li>
             <li class="nav-item" v-if="$root.can('consultar_cotizacion_compra')" style="text-align: left">
                 <router-link :to="{name: 'cotizacion'}" class="nav-link" :class="{active: this.$route.name == 'cotizacion'}">
                     <i class="fa fa-comment-dollar nav-icon"></i>
                     Cotizaciones
                 </router-link>
             </li>
             <li class="nav-item" v-if="$root.can('consultar_asignacion_proveedor')" style="text-align: left">
                 <router-link :to="{name: 'asignacion-proveedor'}" class="nav-link" :class="{active: this.$route.name == 'asignacion-proveedor'}">
                     <i class="fa fa-user-check nav-icon"></i>
                     Asignación de Proveedores
                 </router-link>
             </li>
            <li class="nav-item" v-if="$root.can('consultar_orden_compra')" style="text-align: left">
                <router-link :to="{name: 'orden-compra'}" class="nav-link" :class="{active: this.$route.name == 'orden-compra'}">
                    <i class="fa fa-shopping-cart nav-icon"></i>
                    Órdenes de Compra
                </router-link>
            </li>
         </ul>
     </nav>
    </span>
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
