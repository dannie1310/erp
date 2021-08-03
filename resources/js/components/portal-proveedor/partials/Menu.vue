<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">COTIZACIONES</li>
            <li class="nav-item"  v-if="$root.can('registrar_cotizacion_proveedor',true)">
                <router-link :to="{name: 'cotizacion-proveedor-seleccionar-solicitud'}" class="nav-link" :class="{active: this.$route.name == 'cotizacion-proveedor-seleccionar-solicitud'}">
                    <i class="fa fa-plus nav-icon"></i>
                    <p>Registrar Cotización</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('registrar_cotizacion_proveedor',true)">
                <router-link :to="{name: 'cotizacion-proveedor-para-envio'}" class="nav-link" :class="{active: this.$route.name == 'cotizacion-proveedor-para-envio'}">
                    <i class="fa fa-send nav-icon"></i>
                    <p>Enviar Cotización</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_cotizacion_proveedor',true)">
                <router-link :to="{name: 'cotizacion-proveedor'}" class="nav-link">
                    <i class="fa fa-comment-dollar nav-icon"></i>
                    <p>Lista de Cotizaciones</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_cotizacion_proveedor',true)">
                <router-link :to="{name: 'invitacion-proveedor'}" class="nav-link">
                    <i class="fa fa-envelope nav-icon"></i>
                    <p>Lista de Invitaciones a Cotizar</p>
                </router-link>
            </li>

        </ul>
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-header">SOLICITUDES DE REVISIÓN DE CFDI</li>
            <li class="nav-item"  v-if="$root.can('registrar_solicitud_recepcion_cfdi',true)">
                <router-link :to="{name: 'seleccionar-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'seleccionar-cfdi'}">
                    <i class="fa fa-plus nav-icon"></i>
                    <p>Registrar Solicitud</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('aprobar_solicitud_recepcion_cfdi')">
                <router-link :to="{name: 'recepcion-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'recepcion-cfdi'}">
                    <i class="fa fa-check-circle nav-icon"></i>
                    <p>Recibir CFDI</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_solicitud_recepcion_cfdi_proveedor',true)">
                <router-link :to="{name: 'entrega-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'entrega-cfdi'}">
                    <i class="fa fa-copy nav-icon"></i>
                    <p>Lista de Solicitudes de Revisión</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_solicitud_recepcion_cfdi_proyecto',true)">
                <router-link :to="{name: 'solicitud-recepcion-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-recepcion-cfdi'}">
                    <i class="fa fa-copy nav-icon"></i>
                    <p>Lista de Solicitudes de Revisión</p>
                </router-link>
            </li>
        </ul>
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-header">EXPEDIENTE</li>
            <li class="nav-item"  v-if="$root.can('registrar_solicitud_recepcion_cfdi',true)">
                <router-link :to="{name: 'expediente'}" class="nav-link" :class="{active: this.$route.name == 'expediente'}">
                    <i class="fa fa-folder-open nav-icon"></i>
                    <p>Actualizar Expediente</p>
                </router-link>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>
<script>
    export default {
        name: "proveedor-menu",
        computed: {

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
