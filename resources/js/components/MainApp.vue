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
                    <div>

                        <div class="modal fade" data-backdrop="static" data-keyboard="false" ref="modal" tabindex="-1" role="dialog" aria-labelledby="AvisosModal">
                            <div class="modal-dialog modal-lg" id="mdialTamanio">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title"><i class="fa fa-info-circle"></i></h4>
                                    </div>
                                    <div class="modal-body modal-lg"  ref="body">
                                        <img :src="aviso" style="width:100%">

                                        <!--<video width="1024" height="768" controls autoplay>
                                            <source :src="aviso" type="video/mp4">
                                        </video>-->

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-success" v-on:click="leerAviso" :disabled="leyendo || enviar_bloqueado" >
                                            <span v-if="!enviar_bloqueado">
                                                <i class="fa fa-check"  ></i>
                                                Enterado
                                            </span>
                                            <span v-else>
                                                <i class="fa fa-spinner" ></i>
                                                Leyendo Aviso...
                                            </span>

                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
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
                <MenuAlmacen v-if="acceso_almacenes"></MenuAlmacen>
                <MenuAcarreos v-if="acceso_acarreos"/>
                <MenuContratos v-if="acceso_contratos"></MenuContratos>
                <MenuCatalogos v-if="acceso_catalogos"></MenuCatalogos>
                <MenuEntregaCfdi v-if="acceso_entrega_cfdi"></MenuEntregaCfdi>
                <MenuFinanzas v-if="acceso_finanzas"></MenuFinanzas>
                <MenuContabilidad v-if="acceso_contabilidad"></MenuContabilidad>
                <MenuControlObra v-if="acceso_control_obra" />
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
    import MenuControlObra from "./control-obra/partials/MenuSideControl";

    export default {
        name: 'main-app',
        components: {AppBreadcrumb, AppSidebar, AppHeader, AppFooter, MenuEntregaCfdi,

            MenuAlmacen, MenuCatalogos, MenuCompras, MenuContratos, MenuFinanzas, MenuContabilidad, MenuFormatos, MenuAcarreos, MenuControlObra},
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
                acceso_almacenes : false,
                acceso_control_obra : false,
                aviso : '',
                id_aviso : '',
                leyendo: false,
                enviar_bloqueado : true,
                duracion:0,
            }
        },

        mounted() {
            if(this.$route.name == 'portal')
            {
                this.$store.commit('auth/setObra', { obra: null });
                this.$store.commit('auth/setPermisos', { permisos: [] });
                this.$store.commit('auth/setEmpresa', null);

                this.$session.remove('permisos');
                this.$session.remove('db');
                this.$session.remove('id_obra');
                this.$session.remove('sistemas');
                this.$session.remove('id_empresa');
                this.$session.remove('empresa');
            }

            this.getAviso();
        },

        methods: {
            leerAviso(){
                this.leyendo = true;
                return this.$store.dispatch('seguridad/sistema/leerAviso', {
                    id:this.id_aviso
                })
                .then(data => {
                }).finally( ()=>{
                    $(this.$refs.modal).modal('hide');
                    this.leyendo = false;
                });
            },
            getAviso(){
                let _self = this;
                return this.$store.dispatch('seguridad/sistema/getAviso', {
                    ruta:this.$router.currentRoute.name
                })
                    .then(data => {
                        this.aviso = data.ruta_aviso;
                        this.id_aviso = data.id;
                        this.duracion = 1000*data.duracion;
                    }).finally( ()=>{
                        if(this.aviso){
                            $(this.$refs.modal).appendTo('body')
                            $(this.$refs.modal).modal('show');
                            setTimeout(function(){
                               _self.enviar_bloqueado = false;
                            }, _self.duracion);
                        }
                    });
            }
        },

        watch:{
            sistemas(){
                this.acceso_compras = this.sistemas.find(x=>x.url === 'compras') !== undefined ? true : false;
                this.acceso_almacenes = this.sistemas.find(x=>x.url === 'almacenes') !== undefined ? true : false;
                this.acceso_acarreos = this.sistemas.find(x=>x.url === 'acarreos') !== undefined ? true : false;
                this.acceso_contratos = this.sistemas.find(x=>x.url === 'contratos') !== undefined ? true : false;
                this.acceso_catalogos = this.sistemas.find(x=>x.url === 'catalogos') !== undefined ? true : false;
                this.acceso_finanzas = this.sistemas.find(x=>x.url === 'finanzas') !== undefined ? true : false;
                this.acceso_contabilidad = this.sistemas.find(x=>x.url === 'sistema_contable') !== undefined ? true : false;
                this.acceso_entrega_cfdi = this.sistemas.find(x=>x.url === 'recepcion-cfdi') !== undefined ? true : false;
                this.acceso_control_obra = this.sistemas.find(x => x.url === 'control_obra') !== undefined ? true :false;
            },
            $route (to, from){
                this.getAviso();
            }
        },
        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            },
            currentObra() {
                return this.$store.getters['auth/currentObra']
            },
            currentEmpresa(){
                return this.$store.getters['auth/currentEmpresa']
            },
            sistemas() {
                return this.$store.getters['seguridad/sistema/sistemas']
            },
            url() {
                return process.env.MIX_APP_URL;
            },

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
