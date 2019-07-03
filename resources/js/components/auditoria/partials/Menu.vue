<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item" v-if="$root.can('auditoria_consultar_permisos_por_obra',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <p>
                        CONSULTA DE PERMISOS
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" v-if="$root.can('auditoria_consultar_permisos_por_obra',true)">
                    <li class="nav-item">
                        <router-link :to="{name: 'por-obra'}" class="nav-link" :class="{active: this.$route.name == 'por-obra'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Permisos por Obra</p>
                        </router-link>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "auditoria-menu",
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
