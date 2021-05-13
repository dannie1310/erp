<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"  v-if="$root.can('consultar_autocorreccion_cfd_efo',true) || $root.can('consultar_cfdi',true)
            || $root.can('consultar_informe_cfd_x_empresa_x_mes',true)
            || $root.can('consultar_no_deducido_cfd_efo',true)
            || $root.can('consultar_informe_cfd_x_empresa_desglosado',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-file-invoice nav-icon"></i>
                    <p>CFDI</p>
                    <i class="right fa fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"  v-if="$root.can('consultar_informe_cfd_x_empresa_x_mes',true)">
                        <router-link :to="{name: 'informe-cfd-empresa-tiempo'}" class="nav-link" :class="{active: this.$route.name == 'informe-cfd-empresa-tiempo'}">
                            <i class="fa fa-file-alt nav-icon"></i>
                            <p>Informe CFDI x Empresa x Mes</p>
                        </router-link>
                    </li>
                    <li class="nav-item"  v-if="$root.can('consultar_informe_cfdi_x_empresa_desglosado',true)">
                        <router-link :to="{name: 'informe-cfdi-completo'}" class="nav-link" :class="{active: this.$route.name == 'informe-cfdi-completo'}">
                            <i class="fa fa-file-alt nav-icon"></i>
                            <p>Informe Completo de CFDI</p>
                        </router-link>
                    </li>
                    <li class="nav-item"  v-if="$root.can('consultar_cfdi',true)">
                        <router-link :to="{name: 'cfd-sat'}" class="nav-link" :class="{active: this.$route.name == 'cfd-sat'}">
                            <i class="fa fa-file-invoice nav-icon"></i>
                            <p>CFDI SAT</p>
                        </router-link>
                    </li>
                    <li class="nav-item"   v-if="$root.can('consultar_autocorreccion_cfd_efo',true)">
                        <router-link :to="{name: 'autocorreccion-cfd-efos'}" class="nav-link" :class="{active: this.$route.name == 'autocorreccion-cfd-efos'}">
                            <i class="fa fa-check-circle nav-icon"></i>
                            <p>Autocorrección CFDI EFOS</p>
                        </router-link>
                    </li>
                    <li class="nav-item"   v-if="$root.can('consultar_no_deducido_cfd_efo',true)">
                        <router-link :to="{name: 'no-deducidos-cfd-efos'}" class="nav-link" :class="{active: this.$route.name == 'no-deducidos-cfd-efos'}">
                            <i class="fa fa-check-square nav-icon"></i>
                            <p>CFDI No Deducidos de EFOS</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_informe_listado_efos_vs_cfdi_recibidos',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-user-slash nav-icon"></i>
                    <p>EFOS</p>
                    <i class="right fa fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"  v-if="$root.can('consultar_informe_listado_efos_vs_cfdi_recibidos',true)">
                        <router-link :to="{name: 'informe-efos-vs-cfd'}" class="nav-link" :class="{active: this.$route.name == 'informe-efos-vs-cfd'}">
                            <i class="fa fa-file-alt nav-icon"></i>
                            <p>Informe EFOS vs CFDI</p>
                        </router-link>
                    </li>
                    <li class="nav-item"  v-if="$root.can('consultar_informe_listado_efos_vs_cfdi_recibidos',true)">
                        <router-link :to="{name: 'informe-efos-vs-cfd-5a'}" class="nav-link" :class="{active: this.$route.name == 'informe-efos-vs-cfd-5a'}">
                            <i class="fa fa-file-alt nav-icon"></i>
                            <p>Informe EFOS vs CFDI (Desglosado)</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_proveedores_no_localizados',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-user-ninja nav-icon"></i>
                    <p>No Localizados</p>
                    <i class="right fa fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"  v-if="$root.can('consultar_proveedores_no_localizados',true)">
                        <router-link :to="{name: 'no-localizados'}" class="nav-link" :class="{active: this.$route.name == 'no-localizados'}">
                            <i class="fa fa-file-alt nav-icon"></i>
                            <p>No Localizados SAT</p>
                        </router-link>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "fiscal-menu",
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }
    }
</script>



<style scoped>

</style>
