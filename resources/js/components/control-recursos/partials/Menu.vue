<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header" style="text-align: center">
                <b>CONTROL DE RECURSOS</b>
                <hr style="border-color: #9e9e9e; margin-bottom: 3px">
            </li>
            <li class="nav-item">
                <router-link :to="{name: 'factura-recurso'}" v-if="$root.can('consultar_factura_recursos',true)" class="nav-link" :class="{active: this.$route.name == 'factura-recurso'}">
                    <i class="nav-icon fa fa-file-invoice"></i>
                    <p>Factura</p>
                </router-link>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-money-check"></i>
                    <p>
                        Gastos del Personal
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_factura_recursos',true)">
                        <router-link :to="{name: 'relacion-gasto'}" class="nav-link" :class="{active: this.$route.name == 'relacion-gasto'}">
                            &nbsp;<i class="fa fa-money-check nav-icon"></i>
                            <p>Relaciones de Gastos</p>
                        </router-link>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "control-recurso-menu",
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
