<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->

            <li class="nav-header" style="text-align: center">
                <b>SISTEMA DE FINANZAS</b>
                <hr style="border-color: #9e9e9e; margin-bottom: 3px">
            </li>

            <li class="nav-item" v-if="$root.can('consultar_banco')">
                <router-link :to="{name: 'banco'}" class="nav-link" :class="{active: this.$route.name == 'banco'}">
                    <i class="fa fa-piggy-bank nav-icon"></i>
                    <p>Gestión de Bancos</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_fondos')">
                <router-link :to="{name: 'fondo'}" class="nav-link" :class="{active: this.$route.name == 'fondo'}">
                    <i class="fa fa-money-check-alt nav-icon"></i>
                    <p>Gestión de Fondos</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_comprobante_fondo')">
                <router-link :to="{name: 'comprobante-fondo'}" class="nav-link" :class="{active: this.$route.name == 'comprobante-fondo'}">
                    <i class="fa fa-coins nav-icon"></i>
                    <p>Comprobantes de Fondo</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="cuenta_bancaria">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-money-check"></i>
                    <p>
                        Gestión de Cuentas Bancarias
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_cuentas_bancarias_empresa')">
                        <router-link :to="{name: 'cuenta-empresa-bancaria'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-empresa-bancaria'}">
                            &nbsp;<i class="fa fa-money-check nav-icon"></i>
                            <p>Cuentas Bancarias</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_solicitud_alta_cuenta_bancaria_empresa')">
                        <router-link :to="{name: 'solicitud-alta'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-alta'}">
                            &nbsp;<i class="fa fa-plus-square nav-icon"></i>
                            <p>Solicitud de Alta</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_solicitud_baja_cuenta_bancaria_empresa')">
                        <router-link :to="{name: 'solicitud-baja'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-baja'}">
                            &nbsp;<i class="fa fa-minus-square nav-icon"></i>
                            <p>Solicitud de Baja</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_solicitud_pago_anticipado')">
                <router-link :to="{name: 'pago-anticipado'}" class="nav-link" :class="{active: this.$route.name == 'pago-anticipado'}">
                    &nbsp;<i class="fa fa-file-powerpoint nav-icon"></i>
                    <p>Solicitud de Pago Anticipado</p>
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_distribucion_recursos_remesa')">
                <router-link :to="{name: 'distribuir-recurso-remesa'}" class="nav-link" :class="{active: this.$route.name == 'distribuir-recurso-remesa'}">
                    <i class="fa fa-coins nav-icon"></i>
                    <p>Dispersión de Recursos</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_pagos')">
                <router-link :to="{name: 'pago'}" class="nav-link" :class="{active: this.$route.name == 'pago'}">
                    &nbsp;<i class="fa fa-hand-holding-usd nav-icon"></i>
                    <p>Pagos</p>
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_factura')">
                <router-link :to="{name: 'factura'}" class="nav-link" :class="{active: this.$route.name == 'factura'}">
                    <i class="nav-icon fa fa-file-invoice"></i>
                    <p>Facturas</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_movimiento_bancario')">
                <router-link :to="{name: 'movimiento-bancario'}" class="nav-link" :class="{active: this.$route.name == 'movimiento-bancario'}">
                    &nbsp;<i class="fa fa-receipt nav-icon"></i>
                    <p>Movimientos Bancarios</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_traspaso_cuenta')">
                <router-link :to="{name: 'traspaso-entre-cuentas'}" class="nav-link" :class="{active: this.$route.name == 'traspaso-entre-cuentas'}">
                    &nbsp;<i class="fa fa-retweet nav-icon"></i>
                    <p>Traspasos Entre Cuentas</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_cfdi')">
                <router-link :to="{name: 'cfdi'}" class="nav-link" :class="{active: this.$route.name == 'cfdi'}">
                    &nbsp;<i class="fa fa-file-code nav-icon"></i>
                    <p>CFDI</p>
                </router-link>
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
                    'consultar_pagos',
                    'consultar_carga_layout_pago'
                ]);
            },
            cuenta_bancaria(){
                return this.$root.can([
                    'consultar_solicitud_alta_cuenta_bancaria_empresa',
                    'consultar_solicitud_baja_cuenta_bancaria_empresa'
                ]);
            },
            tesoreria(){
                return this.$root.can([
                    'consultar_movimiento_bancario',
                    'consultar_traspaso_cuenta'
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
