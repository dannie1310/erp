<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->

            <li class="nav-header" style="text-align: center">
                <b>SISTEMA DE CONTABILIDAD</b>
                <hr style="border-color: #9e9e9e; margin-bottom: 3px">
            </li>

            <li class="nav-item" v-if="catalogos">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-list"></i>
                    <p>
                        Cuentas Contables
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_cuenta_almacen')">
                        <router-link :to="{name: 'cuenta-almacen'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-almacen'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cuentas de Almacén</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_cuenta_contable_bancaria')">
                        <router-link :to="{name: 'cuenta-banco'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-banco'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cuentas de Banco</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_cuenta_concepto')">
                        <router-link :to="{name: 'cuenta-concepto'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-concepto'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cuentas de Concepto</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_cuenta_costo')">
                        <router-link :to="{name: 'cuenta-costo'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-costo'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cuentas de Costo</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_cuenta_empresa')">
                        <router-link :to="{name: 'cuenta-empresa'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-empresa'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cuentas de Empresa</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_cuenta_fondo')">
                        <router-link :to="{name: 'cuenta-fondo'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-fondo'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cuentas de Fondo</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_cuenta_general')">
                        <router-link :to="{name: 'cuenta-general'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-general'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cuentas Generales</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_cuenta_material')">
                        <router-link :to="{name: 'cuenta-material'}" class="nav-link" :class="{active: this.$route.name == 'cuenta-material'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Cuentas de Materiales</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="$root.can('consultar_tipo_cuenta_contable')">
                        <router-link :to="{name: 'tipo-cuenta-contable'}" class="nav-link" :class="{active: this.$route.name == 'tipo-cuenta-contable'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Tipos de Cuentas Contables</p>
                        </router-link>
                    </li>
                </ul>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_cierre_periodo')">
                <router-link :to="{name: 'cierre-periodo'}" class="nav-link" :class="{active: this.$route.name == 'cierre-periodo'}">
                    <i class="fa fa-ban nav-icon"></i>
                    <p>Cierres de periodo</p>
                </router-link>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_prepolizas_generadas')">
                <router-link :to="{name: 'poliza'}" class="nav-link" :class="{active: this.$route.name == 'poliza'}">
                    <i class="fa fa-file-text nav-icon"></i>
                    <p>Prepólizas Generadas</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar-cfdi-pendientes-carga-add')">
                <router-link :to="{name: 'cfdi-pendientes-carga-add'}" class="nav-link" :class="{active: this.$route.name == 'cfdi-pendientes-carga-add'}">
                    <i class="fa fa-upload nav-icon"></i>
                    <p>Enviar CFDI a ADD Contpaq</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('asociar_poliza_contpaq_cfdi')">
                <router-link :to="{name: 'asociar-poliza-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'asociar-poliza-cfdi'}">
                    <i class="fa fa-share-alt nav-icon"></i>
                    <p>Asociar CFDI con Pólizas Contpaq</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_poliza',true)">
                <router-link :to="{name: 'poliza-cfdi-proyecto'}" class="nav-link" :class="{active: this.$route.name == 'poliza-cfdi-proyecto'}">
                    <i class="fa fa-file-code nav-icon"></i>
                    <p>Pólizas CFDI</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_poliza',true)">
                <router-link :to="{name: 'poliza-contpaq-en-sao'}" class="nav-link" :class="{active: this.$route.name == 'poliza-contpaq-en-sao'}">
                    <i class="fa fa-table nav-icon"></i>
                    <p>Pólizas Contpaq</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_informe_sat')">
                <router-link :to="{name: 'informe-sat'}" class="nav-link" :class="{active: this.$route.name == 'informe-sat'}">
                    <i class="fa fa-file-alt nav-icon"></i>
                    <p>Informe SAT LP 2020</p>
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
