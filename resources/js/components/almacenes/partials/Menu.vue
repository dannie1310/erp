<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>


<!--            <li class="nav-item" v-if="gestion_solicitud">-->
<!--                <a href="#" class="nav-link" @click="mostrarMenu($event)">-->
<!--                    <p>-->
<!--                        Gestión de Solicitudes-->
<!--                        <i class="right fa fa-angle-left"></i>-->
<!--                    </p>-->
<!--                </a>-->
<!--                <ul class="nav nav-treeview">-->
<!--                    <li class="nav-item" v-if="$root.can(['consultar_solicitud_compra'])">-->
<!--                        <router-link :to="{name: 'solicitud-compra'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-compra'}">-->
<!--                            <i class="fa fa-circle-o nav-icon"></i>-->
<!--                            <p>Solicitudes</p>-->
<!--                        </router-link>-->
<!--                    </li>-->
<!--                </ul>-->
<!--            </li>-->
<!--            <li class="nav-item" v-if="gestion_orden_compra">-->
<!--                <a href="#" class="nav-link" @click="mostrarMenu($event)">-->
<!--                    <p>-->
<!--                        Gestión de OC-->
<!--                        <i class="right fa fa-angle-left"></i>-->
<!--                    </p>-->
<!--                </a>-->

<!--                <ul class="nav nav-treeview">-->
<!--                    <li class="nav-item" v-if="$root.can('consultar_orden_compra')">-->
<!--                        <router-link :to="{name: 'orden-compra'}" class="nav-link" :class="{active: this.$route.name == 'orden-compra'}">-->
<!--                            <i class="fa fa-circle-o nav-icon"></i>-->
<!--                            <p>Ordenes de Compra</p>-->
<!--                        </router-link>-->
<!--                    </li>-->
<!--                </ul>-->
<!--            </li>-->
<!--            <li class="nav-item" v-if="gestion_almacen">-->
<!--                <a href="#" class="nav-link" @click="mostrarMenu($event)">-->
<!--                    <i class="nav-icon fa fa-server"></i>-->
<!--                    <p>-->
<!--                        Gestión de Almacén-->
<!--                        <i class="right fa fa-angle-left"></i>-->
<!--                    </p>-->
<!--                </a>-->

<!--                <ul class="nav nav-treeview">-->
<!--                    <li class="nav-item" v-if="$root.can('consultar_entrada_almacen')">-->
<!--                        <router-link :to="{name: 'entrada-almacen'}" class="nav-link" :class="{active: this.$route.name == 'entrada-almacen'}">-->
<!--                            <i class="fa fa-circle-o nav-icon"></i>-->
<!--                            <p>Entrada</p>-->
<!--                        </router-link>-->
<!--                    </li>-->
<!--                </ul>-->
<!--                <ul class="nav nav-treeview">-->
<!--                    <li class="nav-item" v-if="$root.can('consultar_salida_almacen')">-->
<!--                        <router-link :to="{name: 'salida-almacen'}" class="nav-link" :class="{active: this.$route.name == 'salida-almacen'}">-->
<!--                            <i class="fa fa-circle-o nav-icon"></i>-->
<!--                            <p>Salida</p>-->
<!--                        </router-link>-->
<!--                    </li>-->
<!--                </ul>-->
<!--            </li>-->
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "almacenes-menu",

        computed: {
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
