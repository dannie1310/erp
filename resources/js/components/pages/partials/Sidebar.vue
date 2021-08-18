<template>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <router-link :to="{name: 'portal'}" class="brand-link" v-if="$router.currentRoute.name != 'portal'">
            <span v-if="logo=='sao'">
                <img src="../../../../img/company-icon.png"
                     alt="Sao Logo"
                     class="brand-image img-circle elevation-3"
                     style="opacity: .8" >
                <span class="brand-text font-weight-light">{{sidebar}}</span>
            </span>
            <div class="row" v-else-if="logo=='portal-proveedores'">
                <div class="col-md-2" style="padding-top: 0.2rem"><h4 ><i  class="fa fa-users"></i></h4></div>
                <div class="col-md-10" style="padding-top: 0.2rem"> <span class="brand-text font-weight-light">{{sidebar}}</span></div>
            </div>
        </router-link>

        <!-- Sidebar -->
        <div class="sidebar" v-if="$router.currentRoute.name != 'portal'">
            <router-view name="menu"></router-view>
        </div>
        <!-- /.sidebar -->
    </aside>
</template>

<script>
    export default {
        name: "app-sidebar",
        props: ['sidebar', 'logo'],
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
