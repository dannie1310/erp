<template>
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-header"><a href="#Configuracion">ASIGNAR USUARIO</a></li>
            <li class="nav-item">
                <a href="#subcontratantes" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Áreas Subcontratantes</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#compradoras" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Áreas Compradoras</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#solicitantes" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Áreas Solicitantes</p>
                </a>
            </li>
        </ul>
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-header">CONFIGURACIÓN</li>
            <li class="nav-item">
                <router-link :to="{name: 'configuracion-general-obra'}" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Configuración de Obra</p>
                </router-link>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        name: "menu-configuracion"
    }
</script>

<style scoped>
    .sidebar-form, .nav-sidebar > .nav-header {
        padding: 1rem 0.5rem 0.5rem 1rem;
    }
</style>
