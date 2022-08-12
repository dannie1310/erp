<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item" v-if="$root.can('consulta_resguardo_activo_fijo',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="fas fa-file-signature"></i>
                    <p>Resguardos</p>
                    <i class="right fa fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consulta_resguardo_activo_fijo',true)">
                        <router-link :to="{name: 'resguardos-index'}" class="nav-link" :class="{active: this.$route.name == 'resguardos-index'}">
                            <i class="fa fa-search nav-icon"></i>
                            <p>Consultar</p>
                        </router-link>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "activo-fijo-menu",
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }
    }
</script>

