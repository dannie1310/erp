<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-justified control-sidebar-menu flex-column"  role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->

            <li class="nav-header" style="text-align: center;">
                <hr style="margin: 0px">
                <router-link :to="{name: 'sistema_contable'}" class="nav-link" :class="{active: this.$route.name == 'sistema_contable'}">
                    <b>SISTEMA DE CONTABILIDAD</b>
                </router-link>
                <hr style="margin: 0px">
            </li>

            <li class="nav-item" v-if="$root.can('consultar_cuenta_almacen')" style="text-align: left;">
                <router-link :to="{name: 'cuenta-almacen'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-almacen'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    Cuentas de Almacén
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_cuenta_contable_bancaria')" style="text-align: left;">
                <router-link :to="{name: 'cuenta-banco'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-banco'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    Cuentas de Banco
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_cuenta_concepto')" style="text-align: left;">
                <router-link :to="{name: 'cuenta-concepto'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-concepto'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    Cuentas de Concepto
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_cuenta_costo')" style="text-align: left;">
                <router-link :to="{name: 'cuenta-costo'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-costo'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    Cuentas de Costo
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_cuenta_empresa')" style="text-align: left;">
                <router-link :to="{name: 'cuenta-empresa'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-empresa'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    Cuentas de Empresa
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_cuenta_fondo')" style="text-align: left;">
                <router-link :to="{name: 'cuenta-fondo'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-fondo'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    Cuentas de Fondo
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_cuenta_general')" style="text-align: left;">
                <router-link :to="{name: 'cuenta-general'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-general'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    Cuentas Generales
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_cuenta_material')" style="text-align: left;">
                <router-link :to="{name: 'cuenta-material'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-material'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    Cuentas de Materiales
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_tipo_cuenta_contable')" style="text-align: left;">
                <router-link :to="{name: 'tipo-cuenta-contable'}" class="nav-link" :class="{active: this.$route.name == 'tipo-cuenta-contable'}">
                    <i class="fa fa-circle-o nav-icon"></i>
                    Tipos de Cuentas Contables
                </router-link>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_cierre_periodo')" style="text-align: left;">
                <router-link :to="{name: 'cierre-periodo'}" class="nav-link" :class="{active: this.$route.name == 'cierre-periodo'}">
                    <i class="fa fa-ban nav-icon"></i>
                    Cierres de periodo
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_prepolizas_generadas')" style="text-align: left;">
                <router-link :to="{name: 'poliza'}" class="nav-link" :class="{active: this.$route.name == 'poliza'}">
                    <i class="fa fa-file-text nav-icon"></i>
                   Prepólizas Generadas
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar-cfdi-pendientes-carga-add')" style="text-align: left;">
                <router-link :to="{name: 'cfdi-pendientes-carga-add'}" class="nav-link" :class="{active: this.$route.name == 'cfdi-pendientes-carga-add'}">
                    <i class="fa fa-upload nav-icon"></i>
                    Enviar CFDI a ADD Contpaq
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('asociar_poliza_contpaq_cfdi')" style="text-align: left;">
                <router-link :to="{name: 'asociar-poliza-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'asociar-poliza-cfdi'}">
                    <i class="fa fa-share-alt nav-icon"></i>
                    Asociar CFDI con Pólizas Contpaq
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_poliza')" style="text-align: left;">
                <router-link :to="{name: 'poliza-cfdi-proyecto'}" class="nav-link" :class="{active: this.$route.name == 'poliza-cfdi-proyecto'}">
                    <i class="fa fa-file-code nav-icon"></i>
                    Pólizas CFDI
                </router-link>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "contabilidad-menu",

        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        },

        computed: {
            catalogos() {
                return this.$root.can([
                    'consultar_cuenta_almacen',
                    'consultar_cuenta_contable_bancaria',
                    'consultar_cuenta_concepto',
                    'consultar_cuenta_costo',
                    'consultar_cuenta_empresa',
                    'consultar_cuenta_fondo',
                    'consultar_cuenta_general',
                    'consultar_cuenta_material'
                ])
            },

            modulos() {
                return this.$root.can([
                    'consultar_cierre_periodo',
                    'consultar_prepolizas_generadas'
                ])
            }
        }
    }
</script>

<style scoped>
    .sidebar-form, .nav-sidebar > .nav-header {
        padding: 1rem 0.5rem 0.5rem 1rem;
    }
</style>
