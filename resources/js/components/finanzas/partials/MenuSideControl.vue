<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-justified control-sidebar-menu flex-column"  role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->

            <li class="nav-header" style="text-align: center;">
                <hr style="margin: 0px">
                <router-link :to="{name: 'finanzas'}" class="nav-link" :class="{active: this.$route.name == 'finanzas'}">
                    <b>SISTEMA DE FINANZAS</b>
                </router-link>
                <hr style="margin: 0px">
            </li>


            <li class="nav-item" v-if="$root.can('consultar_banco')" style="text-align: left;">
                <router-link :to="{name: 'banco'}" class="nav-link" :class="{active: this.$route.name == 'banco'}">
                    <i class="fa fa-piggy-bank nav-icon"></i>
                    Gestión de Bancos
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_fondos')" style="text-align: left;">
                <router-link :to="{name: 'fondo'}" class="nav-link" :class="{active: this.$route.name == 'fondo'}">
                    <i class="fa fa-money-check-alt nav-icon"></i>
                    Gestión de Fondos
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_comprobante_fondo')" style="text-align: left;">
                <router-link :to="{name: 'comprobante-fondo'}" class="nav-link" :class="{active: this.$route.name == 'comprobante-fondo'}">
                    <i class="fa fa-coins nav-icon"></i>
                    Comprobantes de Fondo
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_cuentas_bancarias_empresa')" style="text-align: left;">
                <router-link :to="{name: 'cuenta-empresa-bancaria'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-empresa-bancaria'}">
                    &nbsp;<i class="fa fa-money-check nav-icon"></i>
                    Cuentas Bancarias
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_solicitud_alta_cuenta_bancaria_empresa')" style="text-align: left;">
                <router-link :to="{name: 'solicitud-alta'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-alta'}">
                    &nbsp;<i class="fa fa-plus-square nav-icon"></i>
                    Solicitud de Alta Cuentas Bancaria
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_solicitud_baja_cuenta_bancaria_empresa')" style="text-align: left;">
                <router-link :to="{name: 'solicitud-baja'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-baja'}">
                    &nbsp;<i class="fa fa-minus-square nav-icon"></i>
                    Solicitud de Baja Cuentas Bancaria
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_solicitud_pago_anticipado')" style="text-align: left;">
                <router-link :to="{name: 'pago-anticipado'}" class="nav-link" :class="{active: this.$route.name == 'pago-anticipado'}">
                    &nbsp;<i class="fa fa-file-powerpoint nav-icon"></i>
                    Solicitud de Pago Anticipado
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_distribucion_recursos_remesa')" style="text-align: left;">
                <router-link :to="{name: 'distribuir-recurso-remesa'}" class="nav-link" :class="{active: this.$route.name == 'distribuir-recurso-remesa'}">
                    <i class="fa fa-coins nav-icon"></i>
                    Dispersión de Recursos
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_pagos')" style="text-align: left;">
                <router-link :to="{name: 'pago'}" class="nav-link" :class="{active: this.$route.name == 'pago'}">
                    &nbsp;<i class="fa fa-hand-holding-usd nav-icon"></i>
                    Pagos
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_factura')" style="text-align: left;">
                <router-link :to="{name: 'factura'}" class="nav-link" :class="{active: this.$route.name == 'factura'}">
                    <i class="nav-icon fa fa-file-invoice"></i>
                    Facturas
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_movimiento_bancario')" style="text-align: left;">
                <router-link :to="{name: 'movimiento-bancario'}" class="nav-link" :class="{active: this.$route.name == 'movimiento-bancario'}">
                    &nbsp;<i class="fa fa-receipt nav-icon"></i>
                    Movimientos Bancarios
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_traspaso_cuenta')" style="text-align: left;">
                <router-link :to="{name: 'traspaso-entre-cuentas'}" class="nav-link" :class="{active: this.$route.name == 'traspaso-entre-cuentas'}">
                    &nbsp;<i class="fa fa-retweet nav-icon"></i>
                    Traspasos Entre Cuentas
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_cfdi')" style="text-align: left;">
                <router-link :to="{name: 'cfdi'}" class="nav-link" :class="{active: this.$route.name == 'cfdi'}">
                    &nbsp;<i class="fa fa-file-code nav-icon"></i>
                    CFDI
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
