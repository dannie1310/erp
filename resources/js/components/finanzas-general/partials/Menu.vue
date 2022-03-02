<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item" v-if="$root.can('consultar_solicitudes_pago_global',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-file-powerpoint"></i>
                    <p>Solicitudes de Pago</p>
                    <i class="right fa fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview" style="height: auto">
                    <li class="nav-item" >
                        <router-link :to="{name: 'autorizar-solicitudes-pago'}" class="nav-link" :class="{active: this.$route.name == 'autorizar-solicitudes-pago'}">
                            <i class="fa fa-thumbs-o-up nav-icon"></i>
                            <p> Autorización de Solicitudes</p>
                        </router-link>
                    </li>
                </ul>
                <!--<ul class="nav nav-treeview" style="height: auto">
                    <li class="nav-item" v-if="$root.can('consultar_transaccion_proveedor_no_localizado',true)">
                        <router-link :to="{name: 'enlistar-solicitudes-pago'}" class="nav-link" :class="{active: this.$route.name == 'enlistar-solicitudes-pago'}">
                            <i class="fa fa-list-alt nav-icon"></i>
                            <p> Listado de Solicitudes</p>
                        </router-link>
                    </li>
                </ul>-->
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "finanzas-general-menu",
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
