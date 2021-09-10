<template>
    <body class="hold-transition sidebar-mini layout-footer-fixed layout-navbar-fixed" :class="$router.currentRoute.name == 'portal' ? 'sidebar-collapse' : ''">
    <vue-progress-bar></vue-progress-bar>
    <!-- Site wrapper -->
    <div v-if="currentUser && $router.currentRoute.name != 'google-2fa' &&  $router.currentRoute.name.indexOf('modal') ===-1" class="wrapper">
        <AppHeader />
        <AppSidebar v-bind:sidebar="sidebar" v-bind:logo = "logo" />

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h5>{{ this.$route.meta.title }}</h5>
                        </div>
                        <div class="col-sm-6">
                            <AppBreadcrumb v-if="$router.currentRoute.name != 'portal'"/>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <router-view></router-view>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <AppFooter v-if="$router.currentRoute.name != 'portal'" />

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-light" >
            <div   v-if="$router.currentRoute.name != 'portal'" style="padding-bottom: 40px">

                <MenuCompras  v-if="acceso_compras"></MenuCompras>
                <MenuAlmacen></MenuAlmacen>
                <MenuAcarreos v-if="acceso_acarreos"/>
                <MenuContratos v-if="acceso_contratos"></MenuContratos>
                <MenuCatalogos v-if="acceso_catalogos"></MenuCatalogos>
                <MenuEntregaCfdi v-if="acceso_entrega_cfdi"></MenuEntregaCfdi>
                <MenuFinanzas v-if="acceso_finanzas"></MenuFinanzas>
                <MenuContabilidad v-if="acceso_contabilidad"></MenuContabilidad>

            </div>
        </aside>

    </div>
    <div v-else>
        <router-view></router-view>
    </div>
    <!-- ./wrapper -->
    </body>

</template>

<script>
    import AppHeader from "./pages/partials/Header";
    import AppSidebar from "./pages/partials/Sidebar";
    import AppBreadcrumb from "./pages/partials/Breadcrumb";
    import AppFooter from "./pages/partials/Footer";

    import MenuCompras from './compras/partials/MenuSideControl';
    import MenuContratos from './contratos/partials/MenuSideControl';
    import MenuFinanzas from './finanzas/partials/MenuSideControl';
    import MenuContabilidad from './contabilidad/partials/MenuSideControl';
    import MenuAlmacen from './almacenes/partials/MenuSideControl';
    import MenuCatalogos from './catalogos/partials/MenuSideControl';
    import MenuFormatos from './formato/partials/Menu';
    import MenuAcarreos from './acarreos/partials/MenuSideControl';
    import MenuEntregaCfdi from "./solicitud-recepcion-cfdi/partials/MenuSideControl";

    export default {
        name: 'main-app',
        components: {AppBreadcrumb, AppSidebar, AppHeader, AppFooter, MenuEntregaCfdi,

            MenuAlmacen, MenuCatalogos, MenuCompras, MenuContratos, MenuFinanzas, MenuContabilidad, MenuFormatos, MenuAcarreos},
        props: ['sidebar', 'logo'],

        data() {
            return {
                loading: false,
                acceso_acarreos : false,
                acceso_compras : false,
                acceso_contratos : false,
                acceso_catalogos : false,
                acceso_contabilidad : false,
                acceso_finanzas : false,
                acceso_entrega_cfdi : false,


            }
        },

        mounted() {
            this.getSistemas();
        },

        methods: {
            getSistemas() {
                let _self = this;

                return this.$store.dispatch('seguridad/sistema/index', {
                    params: { scope: 'porUsuario'}
                })
                    .then(data => {
                        this.$store.commit('seguridad/sistema/SET_SISTEMAS', data);
                        this.$session.set('sistemas', data);
                        _self.acceso_compras = data.find(x=>x.url === 'compras') !== undefined ? true : false;
                        _self.acceso_acarreos = data.find(x=>x.url === 'acarreos') !== undefined ? true : false;
                        _self.acceso_contratos = data.find(x=>x.url === 'contratos') !== undefined ? true : false;
                        _self.acceso_catalogos = data.find(x=>x.url === 'catalogos') !== undefined ? true : false;
                        _self.acceso_finanzas = data.find(x=>x.url === 'finanzas') !== undefined ? true : false;
                        _self.acceso_contabilidad = data.find(x=>x.url === 'sistema_contable') !== undefined ? true : false;
                        _self.acceso_entrega_cfdi = data.find(x=>x.url === 'recepcion-cfdi') !== undefined ? true : false;
                    })
            }
        },

        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            },
            currentObra() {
                return this.$store.getters['auth/currentObra']
            },
            sistemas() {
                return this.$store.getters['seguridad/sistema/sistemas']
            },
            url() {
                return process.env.MIX_APP_URL;
            }
        }
    }
</script>
<style>
    a {
        color: #68a34d;
    }
    .sidebar .nav-sidebar > .nav-item > .nav-link.active {
         color: #ffffff;
         background-color: #68a34d;
     }
</style>
