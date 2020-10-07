<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->

            <li class="nav-header">FORMATOS</li>
            <li class="nav-item" v-if="contratos">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-building"></i>
                    <p>
                        Contratos
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_formato_orden_pago_estimacion')">
                        <router-link :to="{name: 'orden-pago-estimacion'}" class="nav-link" :class="{active: this.$route.name == 'orden-pago-estimacion'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Orden de Pago Estimación</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_formato_estimacion')">
                        <router-link :to="{name: 'formato-estimacion'}" class="nav-link" :class="{active: this.$route.name == 'formato-estimacion'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Estimación</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item"  v-if="compras">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-cart-arrow-down"></i>
                    <p>
                        Compras
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_orden_compra')">
                        <router-link :to="{name: 'formato-orden-compra'}" class="nav-link" :class="{active: this.$route.name == 'formato-orden-compra'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Orden de Compra</p>
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
        name: "formato-menu",

        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        },
        computed:{
            compras(){
                return this.$root.can([
                    'consultar_orden_compra'
                ]);
            },
            contratos(){
                return this.$root.can(['consultar_formato_orden_pago_estimacion','consultar_formato_estimacion']);
            }

        },
    }
</script>

<style scoped>
    .sidebar-form, .nav-sidebar > .nav-header {
        padding: 1rem 0.5rem 0.5rem 1rem;
    }
</style>
