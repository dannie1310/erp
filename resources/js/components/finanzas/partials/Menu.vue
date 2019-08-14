<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>

            <li class="nav-item" v-if="$root.can('consultar_distribucion_recursos_remesa')">
                <router-link :to="{name: 'distribuir-recurso-remesa'}" class="nav-link">
                    <i class="fa fa-list-alt nav-icon"></i>
                    <p>Dispersión de Recursos</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_banco')">
                <router-link :to="{name: 'banco'}" class="nav-link">
                    <i class="fa fa-list-alt nav-icon"></i>
                    <p>Gestión de Bancos</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_fondos')">
                <router-link :to="{name: 'fondo'}" class="nav-link">
                    <i class="fa fa-list-alt nav-icon"></i>
                    <p>Gestión de Fondos</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="cuenta_bancaria">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-wpforms"></i>
                    <p>
                        Gestión de Cuentas Bancarias
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_solicitud_alta_cuenta_bancaria_empresa')">
                        <router-link :to="{name: 'solicitud-alta'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-alta'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Solicitud de Alta</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_solicitud_cancelacion_cuenta_bancaria_empresa')">
                        <router-link :to="{name: 'solicitud-baja'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-baja'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Solicitud de Baja</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item" v-if="pagos">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-money"></i>
                    <p>
                        Gestión de Pagos
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_pagos')">
                        <router-link :to="{name: 'pago'}" class="nav-link" :class="{active: this.$route.name == 'pago'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Pagos</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item" v-if="solicitudes">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-signal"></i>
                    <p>
                        Solicitudes de Pago
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_solicitud_pago_anticipado')">
                        <router-link :to="{name: 'pago-anticipado'}" class="nav-link" :class="{active: this.$route.name == 'pago-anticipado'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Pago Anticipado</p>
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

        computed:{
            solicitudes() {
                return this.$root.can([
                    'consultar_solicitud_pago_anticipado'
                ])
            },
            pagos() {
                return this.$root.can([
                    'consultar_pagos'
                ]);
            },
            cuenta_bancaria(){
                return this.$root.can([
                    'consultar_solicitud_alta_cuenta_bancaria_empresa',
                    'consultar_solicitud_cancelacion_cuenta_bancaria_empresa'
                ]);
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
