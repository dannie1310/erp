<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>
            <li class="nav-item">
                <a href="#" class="nav-link" >
                    <router-link :to="{name: 'fondo-garantia'}" class="nav-link" :class="{active: this.$route.name == 'estimaciones'}">
                        <i class="nav-icon fa fa-users"></i>
                        <p>
                            Asignación de Contratistas

                        </p>
                    </router-link>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-signal"></i>
                    <p>
                       Estimaciones
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" >
                        <router-link :to="{name: 'estimacion'}" class="nav-link" :class="{active: this.$route.name == 'formato-orden-pago'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Orden de Pago Estimación</p>
                        </router-link>
                    </li>
                </ul>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_fondo_garantia')">
                <a href="#" class="nav-link" >
                    <router-link :to="{name: 'fondo-garantia'}" class="nav-link" :class="{active: this.$route.name == 'fondo-garantia'}">
                        <i class="nav-icon fa fa-money"></i>
                        <p>
                            Fondos de Garantía
                        </p>
                    </router-link>
                </a>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link">
                    <router-link :to="{name: 'fondo-garantia'}" class="nav-link" :class="{active: this.$route.name == 'estimaciones'}">
                        <i class="nav-icon fa fa-sitemap"></i>
                        <p>
                            Contratos Proyectados

                        </p>
                    </router-link>
                </a>

            </li>

            <li class="nav-item">
                <a href="#" class="nav-link" >
                    <router-link :to="{name: 'fondo-garantia'}" class="nav-link" :class="{active: this.$route.name == 'estimaciones'}">
                        <i class="nav-icon fa fa-copy"></i>
                        <p>
                            Presupuestos

                        </p>
                    </router-link>
                </a>

            </li>

            <li class="nav-item">
                <a href="#" class="nav-link">
                    <router-link :to="{name: 'fondo-garantia'}" class="nav-link" :class="{active: this.$route.name == 'estimaciones'}">
                        <i class="nav-icon fa fa-file-text"></i>
                        <p>
                            Subcontratos

                        </p>
                    </router-link>
                </a>

            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "contratos-menu",

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