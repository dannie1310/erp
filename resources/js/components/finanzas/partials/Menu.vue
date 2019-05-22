<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header" v-if="modulos">MÓDULOS</li>

            <li class="nav-item" v-if="modulos">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-signal"></i>
                    <p>
                        Solicitudes de Pago
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_solicitud_pago_anticipado')">
                        <router-link :to="{name: 'pago-anticipado'}" class="nav-link" :class="{active: this.$route.name == 'pago-anticipado'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Pago Anticipado</p>
                        </router-link>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "contratos-menu",

        computed:{
            modulos() {
                return this.$root.can([
                    'consultar_solicitud_pago_anticipado'
                ])
            }
        },
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }

    }
</script>

<style scoped>
    .sidebar-form, .nav-sidebar > .nav-header {
        padding: 1rem 0.5rem 0.5rem 1rem;
    }
</style>