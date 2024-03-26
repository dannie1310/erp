<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"  v-if="$root.can('editar_poliza',true) || $root.can('consultar_poliza',true) || $root.can('asociar-cfdi-a-poliza-contpaq',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-file-powerpoint nav-icon"></i>
                    <p>Gestión de Pólizas</p>
                    <i class="right fa fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"  v-if="$root.can('consultar_poliza',true)">
                        <router-link :to="{name: 'seleccionar-empresa'}" class="nav-link" :class="{active: this.$route.name == 'seleccionar-empresa'}">
                            <i class="fa fa-pencil nav-icon"></i>
                            <p>Editar Pólizas</p>
                        </router-link>
                    </li>
                    <li class="nav-item"  v-if="$root.can('asociar-cfdi-a-poliza-contpaq',true)">
                        <router-link :to="{name: 'seleccionar-empresa-asociacion'}" class="nav-link" :class="{active: this.$route.name == 'seleccionar-empresa-asociacion'}">
                            <i class="fa fa-share-alt nav-icon"></i>
                            <p>Asociar CFDI a Póliza</p>
                        </router-link>
                    </li>
                    <li class="nav-item"  v-if="$root.can('consultar_solicitud_edicion_poliza_ctpq',true)">
                        <router-link :to="{name: 'solicitud-edicion-poliza'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-edicion-poliza'}">
                            <i class="fa fa-file-contract nav-icon"></i>
                            <p>Solicitud de Edición</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item"  v-if="$root.can('editar_poliza',true) || $root.can('consultar_poliza',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-not-equal nav-icon"></i>
                    <p>Gestión de Diferencias</p>
                    <i class="right fa fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"  >
                        <router-link :to="{name: 'informe-diferencia-poliza'}" class="nav-link" :class="{active: this.$route.name == 'informe-diferencia-poliza'}">
                            <i class="fa fa-file-invoice nav-icon"></i>
                            <p>Informe</p>
                        </router-link>
                    </li>
                    <li class="nav-item"  >
                        <router-link :to="{name: 'diferencia-poliza'}" class="nav-link" :class="{active: this.$route.name == 'diferencia-poliza'}">
                            <i class="fa fa-search nav-icon"></i>
                            <p>Búsquedas</p>
                        </router-link>
                    </li>
                    <li class="nav-item"  >
                        <router-link :to="{name: 'diferencia-poliza'}" class="nav-link" :class="{active: this.$route.name == 'diferencia-poliza'}">
                            <i class="fa fa-not-equal nav-icon"></i>
                            <p>Diferencias</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item"  v-if="$root.can('editar_poliza',true) || $root.can('consultar_poliza',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-info-circle nav-icon"></i>
                    <p>Informes</p>
                    <i class="right fa fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"  v-if="$root.can('consultar_poliza',true)">
                        <router-link :to="{name: 'poliza-cfdi'}" class="nav-link" :class="{active: this.$route.name == 'poliza-cfdi'}">
                            <i class="fa fa-file-code nav-icon"></i>
                            <p>Pólizas - CFDI</p>
                        </router-link>
                    </li>
                    <li class="nav-item"  v-if="$root.can('consultar_poliza',true) || true">
                        <router-link :to="{name: 'cuentas-saldo-negativo'}" class="nav-link" :class="{active: this.$route.name == 'cuentas-saldos-negativos'}">
                            <i class="fa fa-minus nav-icon"></i>
                            <p>Cuentas con saldos negativos</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-cogs nav-icon"></i>
                    <p>
                       Configuración
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('configurar_visibilidad_empresa_ctpq', true ) || $root.can('configurar_editabilidad_empresa_ctpq', true ) ">
                        <router-link :to="{name: 'lista-empresa'}" class="nav-link" :class="{active: this.$route.name == 'lista-empresa'}">
                            &nbsp;<i class="fa fa-building nav-icon"></i>
                            <p>Empresas Contpaq</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('editar_empresa_consolidadora', true )">
                        <router-link :to="{name: 'consolidacion'}" class="nav-link" :class="{active: this.$route.name == 'consolidacion'}">
                            &nbsp;<i class="fas fa-handshake nav-icon"></i>
                            <p>Consolidación</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('asociar_cuentas_contpaq_con_proveedor', true )">
                        <router-link :to="{name: 'asociacion-cuenta-proveedor'}" class="nav-link" :class="{active: this.$route.name == 'asociacion-cuenta-proveedor'}">
                            &nbsp;<i class="fas fa-exchange-alt nav-icon"></i>
                            <p>Asociar Cuenta con Proveedor</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_contabilidad_electronica', true )">
                <router-link :to="{name: 'contabilidad-electronica'}" class="nav-link" :class="{active: this.$route.name == 'contabilidad-electronica'}">
                    &nbsp;<i class="fas fa-exchange-alt nav-icon"></i>
                    <p>Lectura de Balanza XML</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_layouts_pasivos', true )">
                <router-link :to="{name: 'layouts-pasivos'}" class="nav-link" :class="{active: this.$route.name == 'layouts-pasivos'}">
                    &nbsp;<i class="fas fa-file-excel nav-icon"></i>
                    <p>Layouts para pasivos IFS</p>
                </router-link>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "contabilidad-general-menu",
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }
    }
</script>

