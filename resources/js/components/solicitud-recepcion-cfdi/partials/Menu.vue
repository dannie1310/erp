<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"  v-if="$root.can('registrar_solicitud_recepcion_cfdi',true)">
                <router-link :to="{name: 'seleccionar-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'seleccionar-cfdi'}">
                    <i class="fa fa-plus nav-icon"></i>
                    <p>Registrar Solicitud</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_solicitud_recepcion_cfdi',true)">
                <router-link :to="{name: 'entrega-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'entrega-cfdi'}">
                    <i class="fa fa-copy nav-icon"></i>
                    <p>Solicitudes de Recepción</p>
                </router-link>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "entrega-cfdi-menu",
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }
    }
</script>

