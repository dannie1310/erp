<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-justified control-sidebar-menu flex-column"  role="menu" data-accordion="false">

            <li class="nav-header" style="text-align: center;">
                <hr style="margin: 0px">
                <router-link :to="{name: 'catalogos'}" class="nav-link" :class="{active: this.$route.name == 'catalogos'}">
                    <b>SOLICITUDES DE REVISIÓN DE CFDI</b>
                </router-link>
                <hr style="margin: 0px">
            </li>

            <li class="nav-item"  v-if="$root.can('registrar_solicitud_recepcion_cfdi',true)" style="text-align: left;">
                <router-link :to="{name: 'seleccionar-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'seleccionar-cfdi'}">
                    <i class="fa fa-plus nav-icon"></i>
                    Registrar Solicitud
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('aprobar_solicitud_recepcion_cfdi')"  style="text-align: left;">
                <router-link :to="{name: 'recepcion-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'recepcion-cfdi'}">
                    <i class="fa fa-check-circle nav-icon"></i>
                    Recibir CFDI
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_solicitud_recepcion_cfdi_proveedor',true)"  style="text-align: left;">
                <router-link :to="{name: 'entrega-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'entrega-cfdi'}">
                    <i class="fa fa-copy nav-icon"></i>
                    Lista de Solicitudes de Revisión
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_solicitud_recepcion_cfdi_proyecto',true)"  style="text-align: left;">
                <router-link :to="{name: 'solicitud-recepcion-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-recepcion-cfdi'}">
                    <i class="fa fa-copy nav-icon"></i>
                    Lista de Solicitudes de Revisión
                </router-link>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "menu-entrega-cfdi",
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }
    }
</script>

