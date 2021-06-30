<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"  v-if="$root.can('consultar_limite_remesa',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fa fa-cog"></i>
                    <p>Configuración</p>
                    <i class="right fa fa-angle-left"></i>
                </a>

                <ul class="nav nav-treeview" >
                    <li class="nav-item">
                        <a href="#" class="nav-link" @click="mostrarMenu($event)">
                            <i class="fa fa-stopwatch"></i>
                            <p>Remesas Extraordinarias</p>
                            <i class="right fa fa-angle-left"></i>
                        </a>
                        <ul class="nav nav-treeview" style="height: auto">
                            <li class="nav-item"  v-if="$root.can('consultar_limite_remesa',true)">
                                <router-link :to="{name: 'limite-remesa'}" class="nav-link" :class="{active: this.$route.name == 'limite-remesa'}">
                                    <i class="fa fa-calendar-week nav-icon"></i>
                                    <p>Límite Semanal</p>
                                </router-link>
                            </li>
                            <li class="nav-item"  v-if="$root.can('consultar_limite_remesa_proyecto',true)">
                                <router-link :to="{name: 'limite-remesa-proyecto'}" class="nav-link" :class="{active: this.$route.name == 'limite-remesa-proyecto'}">
                                    <i class="fa fa-warehouse nav-icon"></i>
                                    <p>Límite Por Proyecto</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>
                </ul>

            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa fa-circle nav-icon"></i>
                    <p>Proveedor No Localizado
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_camion')">
                        <router-link :to="{name: 'autorizar-pago-factura'}" class="nav-link" :class="{active: this.$route.name == 'autorizar-pago-factura'}">
                            <i class="fa fa-truck nav-icon"></i>
                            <p> Autorización Pago Factura</p>
                        </router-link>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "remesas-menu",
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
