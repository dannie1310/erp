<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-justified control-sidebar-menu flex-column"  role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header" style="text-align: center;">
                <hr style="margin: 0px">
                <router-link :to="{name: 'contratos'}" class="nav-link" :class="{active: this.$route.name == 'contratos'}">
                    <b>SISTEMA DE CONTRATOS</b>
                </router-link>
                <hr style="margin: 0px">
            </li>

            <li class="nav-item" v-if="$root.can(['modificar_area_subcontratante_cp', 'consultar_contrato_proyectado'])" style="text-align: left">
                <router-link :to="{name: 'proyectado'}" class="nav-link" :class="{active: this.$route.name == 'proyectado'}">
                    <i class="fa fa-clipboard-list nav-icon"></i>
                    Contrato Proyectado
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_presupuesto_contratista')" style="text-align: left">
                <router-link :to="{name: 'presupuesto'}" class="nav-link" :class="{active: this.$route.name == 'presupuesto'}">
                    <i class="fa fa-comments-dollar nav-icon"></i>
                    Presupuesto Contratista
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can(['consultar_asignacion_contratista'])" style="text-align: left">
                <router-link :to="{name: 'asignacion-contratista'}" class="nav-link" :class="{active: this.$route.name == 'asignacion-contratista'}">
                    <i class="fa fa-user-check nav-icon"></i>
                    Asignación Contratistas
                </router-link>
            </li>
            <li class="nav-item" style="text-align: left">
                <router-link :to="{name: 'subcontrato'}" v-if="$root.can(['consultar_subcontrato'])" class="nav-link" :class="{active: this.$route.name == 'subcontrato'}">
                    <i class="fa fa-file-contract nav-icon"></i>
                    Subcontratos
                </router-link>
            </li>
            <li class="nav-item" style="text-align: left">
                <router-link :to="{name: 'solicitud-cambio'}" v-if="$root.can(['consultar_solicitud_cambio_subcontrato'])" class="nav-link" :class="{active: this.$route.name == 'solicitud-cambio'}">
                    <i class="fa fa-stack-exchange nav-icon"></i>
                    Solicitudes de Cambio
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_estimacion_subcontrato')" style="text-align: left">
                <router-link :to="{name: 'estimacion'}" class="nav-link" :class="{active: this.$route.name == 'estimacion' || this.$route.name == 'estimacion-edit'}">
                    <i class="fa fa-building nav-icon"></i>
                    Estimaciones
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_formato_orden_pago_estimacion')" style="text-align: left">
                <router-link :to="{name: 'formato-orden-pago'}" class="nav-link" :class="{active: this.$route.name == 'formato-orden-pago'}">
                    <i class="fa fa-file-invoice-dollar nav-icon"></i>
                    Orden de Pago Estimación
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_detalle_fondo_garantia') || $root.can('generar_fondo_garantia') || $root.can('consultar_detalle_fondo_garantia')
            || $root.can('registrar_solicitud_movimiento_fondo_garantia')" style="text-align: left">
                <router-link :to="{name: 'fondo-garantia'}" class="nav-link" :class="{active: this.$route.name == 'fondo-garantia'}">
                    <i class="fa fa-piggy-bank nav-icon"></i>
                    Fondos de Garantía
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_solicitud_movimiento_fondo_garantia') || $root.can('registrar_solicitud_movimiento_fondo_garantia') || $root.can('cancelar_solicitud_movimiento_fondo_garantia')
            || $root.can('autorizar_solicitud_movimiento_fondo_garantia') || $root.can('rechazar_solicitud_movimiento_fondo_garantia') || $root.can('revertir_autorizacion_solicitud_movimiento_fondo_garantia')" style="text-align: left">

                <router-link :to="{name: 'solicitud-movimiento-fg'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-movimiento-fg'}">
                    <i class="fa fa-retweet nav-icon"></i>
                    Solicitud de Movimiento a FG
                </router-link>

            </li>
        </ul>
    </nav>
                <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "contratos-menu",

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
