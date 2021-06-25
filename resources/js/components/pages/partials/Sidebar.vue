<template>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <router-link :to="{name: 'home'}" class="brand-link" v-if="$router.currentRoute.name != 'portal'">
            <img src="../../../../img/company-icon.png"
                 alt="Sao Logo"
                 class="brand-image img-circle elevation-3"
                 style="opacity: .8">
            <span class="brand-text font-weight-light">SAO ERP</span>
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
