<template>
    <body class="hold-transition sidebar-mini layout-footer-fixed layout-navbar-fixed" :class="$router.currentRoute.name == 'portal' ? 'sidebar-collapse' : ''">
    <vue-progress-bar></vue-progress-bar>
    <!-- Site wrapper -->
    <div v-if="currentUser && $router.currentRoute.name != 'google-2fa' &&  $router.currentRoute.name.indexOf('modal') ===-1" class="wrapper">
        <AppHeader/>
        <AppSidebar/>
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

        <AppFooter v-if="$router.currentRoute.name != 'portal'"/>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Acceso Rápido</h5>
                <hr class="mb-2">
                <div class="d-block"   v-for="(sistema, i) in sistemas">
                    <router-link :to="{name: sistema.url}" class="d-flex flex-wrap mb-3" v-if="!sistema.externo">
                        <div :class="sistema.color+' elevation-2 text-center'" style="width: 40px; height: 20px; border-radius: 25px; margin-right: 10px; margin-bottom: 10px; opacity: 0.8; cursor: pointer;">
                            <i :class="sistema.icon"></i>
                        </div>
                        {{sistema.name}}
                    </router-link>

                    <a :href="`${sistema.url}?origen=${url}`" target="_self" class="d-flex flex-wrap mb-3" v-else>
                        <div :class="sistema.color+' elevation-2 text-center'" style="width: 40px; height: 20px; border-radius: 25px; margin-right: 10px; margin-bottom: 10px; opacity: 0.8; cursor: pointer;">
                            <i :class="sistema.icon"></i>
                        </div>
                        {{sistema.name}}
                    </a>
                </div>
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
    export default {
        name: 'main-app',
        components: {AppBreadcrumb, AppSidebar, AppHeader, AppFooter},

        data() {
            return {
                loading: false
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
