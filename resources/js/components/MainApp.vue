<template>
    <span>
        <div v-if="currentUser" class="wrapper">
            <app-header/>
            <app-sidebar/>
            <div class="content-wrapper">
                <section class="content-header">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>{{ this.$router.currentRoute.meta.title }}</h1>
                            </div>
                            <div class="col-sm-6">
                                <app-breadcrumb/>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="content">
                    <router-view></router-view>
                </section>
            </div>

            <footer class="main-footer">
                <div class="float-right d-none d-sm-block">
                    <small>{{ currentObra ? currentObra.direccion : '' }}</small>
                </div>
                {{ currentObra ? currentObra.facturar : '' }}
            </footer>

            <aside class="control-sidebar control-sidebar-dark">

            </aside>

        </div>
        <div v-else>
            <router-view></router-view>
        </div>
    </span>
</template>

<script>
    import AppHeader from "./pages/partials/Header";
    import AppSidebar from "./pages/partials/Sidebar";
    import AppBreadcrumb from "./pages/partials/Breadcrumb";
    export default {
        name: 'main-App',
        components: {AppBreadcrumb, AppSidebar, AppHeader},
        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            },
            currentObra() {
                return this.$store.getters['auth/currentObra']
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