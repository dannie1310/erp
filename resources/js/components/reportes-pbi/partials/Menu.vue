<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"  v-if="$root.can('consultar_reporte_cfd_efos',true)">
                <router-link :to="{name: 'visor-reportes'}" class="nav-link" >
                    <i class="fa fa-file-invoice nav-icon"></i>
                    <p>CFD de EFOS</p>
                </router-link>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "reportes-pbi-menu",
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }
    }
</script>

