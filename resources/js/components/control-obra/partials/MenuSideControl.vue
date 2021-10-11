<template>
    <span>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-justified control-sidebar-menu flex-column" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-header" style="text-align: center;">
                    <hr style="margin: 0px">
                    <router-link :to="{name: 'control_obra'}" class="nav-link" :class="{active: this.$route.name == 'control_obra'}">
                        <b>SISTEMA CONTROL DE OBRA</b>
                    </router-link>
                    <hr style="margin: 0px">
                </li>

                <li class="nav-item" v-if="$root.can('consultar_avance_obra')" style="text-align: left">
                    <router-link :to="{name: 'avance-obra'}" class="nav-link" :class="{active: this.$route.name == 'avance-obra'}">
                        <i class="fa fa-circle nav-icon"></i>
                        Avance de Obra
                    </router-link>
                </li>
            </ul>
        </nav>
    </span><!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "control-obra-menu",

        computed: {

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
