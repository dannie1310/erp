<template>
    <body class="hold-transition sidebar-mini">
    <vue-progress-bar></vue-progress-bar>
    <!-- Site wrapper -->
    <div v-if="currentUser" class="wrapper">
        <AppHeader/>
        <AppSidebar/>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>{{ this.$route.meta.title }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <AppBreadcrumb/>
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

        <AppFooter/>


        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Acceso Rápido</h5>
                <hr class="mb-2">
                <div class="d-block"   v-for="(sistema, i) in sistemas">
                    <router-link :to="{name: sistema.url}" class="d-flex flex-wrap mb-3">
                        <div :class="sistema.color+' elevation-2 text-center'" style="width: 40px; height: 20px; border-radius: 25px; margin-right: 10px; margin-bottom: 10px; opacity: 0.8; cursor: pointer;">
                            <i :class="sistema.icon"></i>
                        </div>

                        {{sistema.name}}
                    </router-link>
                </div>


            </div>
        </aside>
        <!-- /.control-sidebar -->
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
        components: {AsignacionRoles, AppBreadcrumb, AppSidebar, AppHeader, AppFooter},

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