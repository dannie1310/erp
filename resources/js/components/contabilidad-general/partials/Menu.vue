<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item"  v-if="$root.can('editar_poliza',true)">
                <router-link :to="{name: 'editar-poliza'}" class="nav-link" :class="{active: this.$route.name == 'editar-poliza'}">
                    <i class="fa fa-circle nav-icon"></i>
                    <p>Editar Póliza</p>
                </router-link>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "contabilidad-general-menu",
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }
    }
</script>

