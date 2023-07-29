<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"  v-if="$root.can('consultar_solicitud_pago_oc',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-file-invoice nav-icon"></i>
                    <p>Solicitud</p>
                    <i class="right fa fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"  v-if="$root.can('consultar_solicitud_pago_oc',true)">
                        <router-link :to="{name: 'pago-oc'}" class="nav-link" :class="{active: this.$route.name == 'pago-oc'}">
                            <i class="fa fa-file-alt nav-icon"></i>
                            <p>Pago OC</p>
                        </router-link>
                    </li>
                    <li class="nav-item"  v-if="$root.can('consultar_informe_cfdi_x_empresa_desglosado',true)">
                        <router-link :to="{name: 'pago-recurrente'}" class="nav-link" :class="{active: this.$route.name == 'pago-recurrente'}">
                            <i class="fa fa-file-alt nav-icon"></i>
                            <p>Pago Recurrente</p>
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
