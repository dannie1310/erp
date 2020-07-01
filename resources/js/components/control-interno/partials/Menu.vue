<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item" v-if="$root.can('auditoria_consultar_permisos_por_obra',true)">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                   <i class="nav-icon fa fa-circle"></i>
                    <p>
                        Consulta de Permisos
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
                <ul class="nav nav-treeview" v-if="$root.can('auditoria_consultar_permisos_por_usuario',true)">
                    <li class="nav-item">
                        <router-link :to="{name: 'por-usuario'}" class="nav-link" :class="{active: this.$route.name == 'por-usuario'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Permisos por Usuario</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item" v-if="$root.can('consultar_efos',true) || $root.can('consultar_transacciones_efos',true) || true">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <i class="nav-icon fa fa-circle"></i>
                    <p>
                        Finanzas
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" v-if="$root.can('consultar_efos',true) || true">
                    <li class="nav-item" >
                        <router-link :to="{name: 'lista-efos'}" class="nav-link" :class="{active: this.$route.name == 'lista-efos'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>EFOS</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview" v-if="$root.can('consultar_transacciones_efos',true) || true">
                    <li class="nav-item" >
                        <router-link :to="{name: 'transaccion-efos'}" class="nav-link" :class="{active: this.$route.name == 'transaccion-efos'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Transacciones con EFOS</p>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_incidencias',true)">
                <router-link :to="{name: 'incidencia'}" class="nav-link">
                    <i class="fa fa-circle nav-icon"></i>
                    <p>Incidencias</p>
                </router-link>
            </li>
            <li class="nav-item"  v-if="$root.can('consultar_incidencias',true)">
                <router-link :to="{name: 'busqueda-empresas-factureras'}" class="nav-link">
                    <i class="fa fa-circle nav-icon"></i>
                    <p>Búsqueda de Factureras</p>
                </router-link>
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
