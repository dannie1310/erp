<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"  v-if="$root.can('consultar_autocorreccion_cfd_efo',true) || $root.can('consultar_poliza',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-file-invoice nav-icon"></i>
                    <p>Gestión de CFD</p>
                    <i class="right fa fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"  v-if="$root.can('consultar_poliza',true)">
                        <router-link :to="{name: 'cfd-sat'}" class="nav-link" :class="{active: this.$route.name == 'cfd-sat'}">
                            <i class="fa fa-file-invoice nav-icon"></i>
                            <p>CFD SAT</p>
                        </router-link>
                    </li>
                    <li class="nav-item"   v-if="$root.can('consultar_poliza',true)">
                        <router-link :to="{name: 'autocorreccion-cfd-efos'}" class="nav-link" :class="{active: this.$route.name == 'autocorreccion-cfd-efos'}">
                            <i class="fa fa-file-invoice nav-icon"></i>
                            <p>Autocorrección CFD EFOS</p>
                        </router-link>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "fiscal-menu",
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
