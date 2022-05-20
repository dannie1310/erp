<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header" style="text-align: center">
                <b>SISTEMA DE CONTRATOS</b>
                <hr style="border-color: #9e9e9e; margin-bottom: 3px">
            </li>

            <li class="nav-item" v-if="$root.can(['modificar_area_subcontratante_cp', 'consultar_contrato_proyectado'])">
                <router-link :to="{name: 'proyectado'}" class="nav-link" :class="{active: this.$route.name == 'proyectado'}">
                    <i class="fa fa-clipboard-list nav-icon"></i>
                    <p>Contrato Proyectado</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_invitacion_cotizar_contrato')">
                <router-link :to="{name: 'invitacion-cotizar-contrato'}" class="nav-link" :class="{active: this.$route.name == 'invitacion-cotizar-contrato'}">
                    <i class="fa fa-envelope-open-text nav-icon"></i>
                    <p>Invitaciones a Cotizar</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_presupuesto_contratista')">
                <router-link :to="{name: 'presupuesto'}" class="nav-link" :class="{active: this.$route.name == 'presupuesto'}">
                    <i class="fa fa-comments-dollar nav-icon"></i>
                    <p>Presupuesto Contratista</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_presupuesto_contratista')">
                <router-link :to="{name: 'comparativa-cotizacion-contrato'}" class="nav-link" :class="{active: this.$route.name == 'comparativa-cotizacion-contrato'}">
                    <i class="fa fa-less-than-equal nav-icon"></i>
                    <p>Comparativas de Cotización</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can(['consultar_asignacion_contratista'])">
                <router-link :to="{name: 'asignacion-contratista'}" class="nav-link" :class="{active: this.$route.name == 'asignacion-contratista'}">
                    <i class="fa fa-user-check nav-icon"></i>
                    <p>Asignación Contratistas</p>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link :to="{name: 'subcontrato'}" v-if="$root.can(['consultar_subcontrato'])" class="nav-link" :class="{active: this.$route.name == 'subcontrato'}">
                    <i class="fa fa-file-contract nav-icon"></i>
                    <p>Subcontratos</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_avance_subcontrato')">
                <router-link :to="{name: 'avance-subcontrato'}" class="nav-link" :class="{active: this.$route.name == 'avance-subcontrato'}">
                    <i class="fa fa-clipboard-list nav-icon"></i>
                    <p>Avance de Subcontrato</p>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link :to="{name: 'solicitud-cambio'}" v-if="$root.can(['consultar_solicitud_cambio_subcontrato'])" class="nav-link" :class="{active: this.$route.name == 'solicitud-cambio'}">
                    <i class="fa fa-stack-exchange nav-icon"></i>
                    <p>Solicitudes de Cambio</p>
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_estimacion_subcontrato')">
                <router-link :to="{name: 'estimacion'}" class="nav-link" :class="{active: this.$route.name == 'estimacion' || this.$route.name == 'estimacion-edit'}">
                    <i class="fa fa-building nav-icon"></i>
                    <p>Estimaciones</p>
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_formato_orden_pago_estimacion')">
                <router-link :to="{name: 'formato-orden-pago'}" class="nav-link" :class="{active: this.$route.name == 'formato-orden-pago'}">
                    <i class="fa fa-file-invoice-dollar nav-icon"></i>
                    <p>Orden de Pago Estimación</p>
                </router-link>
            </li>


            <li class="nav-item" v-if="$root.can('consultar_solicitud_movimiento_fondo_garantia') || $root.can('registrar_solicitud_movimiento_fondo_garantia') || $root.can('cancelar_solicitud_movimiento_fondo_garantia')
            || $root.can('autorizar_solicitud_movimiento_fondo_garantia') || $root.can('rechazar_solicitud_movimiento_fondo_garantia') || $root.can('revertir_autorizacion_solicitud_movimiento_fondo_garantia') || $root.can('consultar_detalle_fondo_garantia') || $root.can('generar_fondo_garantia') || $root.can('consultar_detalle_fondo_garantia')
            || $root.can('registrar_solicitud_movimiento_fondo_garantia')">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <p>
                        Gestión de Fondos de Garantía
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_detalle_fondo_garantia') || $root.can('generar_fondo_garantia') || $root.can('consultar_detalle_fondo_garantia')
            || $root.can('registrar_solicitud_movimiento_fondo_garantia')">
                        <router-link :to="{name: 'fondo-garantia'}" class="nav-link" :class="{active: this.$route.name == 'fondo-garantia'}">
                            <i class="fa fa-piggy-bank nav-icon"></i>
                            <p>Fondos de Garantía</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_solicitud_movimiento_fondo_garantia') || $root.can('registrar_solicitud_movimiento_fondo_garantia') || $root.can('cancelar_solicitud_movimiento_fondo_garantia')
            || $root.can('autorizar_solicitud_movimiento_fondo_garantia') || $root.can('rechazar_solicitud_movimiento_fondo_garantia') || $root.can('revertir_autorizacion_solicitud_movimiento_fondo_garantia')">

                            <router-link :to="{name: 'solicitud-movimiento-fg'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-movimiento-fg'}">
                                <i class="fa fa-retweet nav-icon"></i>
                                <p>Solicitud de Movimiento</p>
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
