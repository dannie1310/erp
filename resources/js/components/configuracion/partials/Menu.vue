<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header" v-if="$root.can('administracion_configuracion_obra')"><a href="#configuracion-obra">CONFIGURACIÓN DE OBRA</a></li>
            <li class="nav-item" v-if="$root.can('administracion_configuracion_obra')">
                <a href="#identificacion" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Identificación</p>
                </a>
            </li>

            <li class="nav-item" v-if="$root.can('administracion_configuracion_obra')">
                <a href="#informacion_financiera" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Información Financiera</p>
                </a>
            </li>

            <li class="nav-item" v-if="$root.can('administracion_configuracion_obra')">
                <a href="#plan" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Plan de Obra</p>
                </a>
            </li>

            <li class="nav-item" v-if="$root.can('administracion_configuracion_obra')">
                <a href="#seguridad" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Seguridad</p>
                </a>
            </li>

            <li class="nav-item" v-if="$root.can('administracion_configuracion_obra')">
                <a href="#ubicacion" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Ubicación</p>
                </a>
            </li>

            <li class="nav-item" v-if="$root.can('actualizar_estado_obra')">
                <a href="#estado-obra" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Estado de Obra</p>
                </a>
            </li>

            <li class="nav-header" v-if="$root.can('habilitar_deshabilitar_sistema')"><a href="#configuracion-sistema">CONFIGURACIÓN SISTEMAS</a></li>
            <li class="nav-item" v-if="$root.can('habilitar_deshabilitar_sistema')">
                <a href="#configuracion-sistema" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Habilitar Sistemas</p>
                </a>
            </li>

            <li class="nav-header" v-if="$root.can('editar_configuracion_contable')"><a href="#configuracion-contable">CONFIGURACIÓN CONTABLE</a></li>
            <li class="nav-item" v-if="$root.can('editar_configuracion_contable')">
                <a href="#contpaq" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>ContPaq</p>
                </a>
            </li>

            <li class="nav-header" v-if="$root.can(['asignar_roles','registrar_roles','asignar_permisos'])">
                <a href="#configuracion-seguridad">ROLES Y PERMISOS</a></li>
            <li class="nav-item" v-if="$root.can('asignar_roles')">
                <a href="#asignacion"  class="nav-link" >
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Asignar Roles</p>
                </a>
            </li>

            <li class="nav-item" v-if="$root.can('asignar_roles')">
                <a href="#desasignacion" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Desasignar Roles</p>
                </a>
            </li>

            <li class="nav-item" v-if="$root.can('registrar_roles')">
                <a href="#seguridad-rol-create" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Crear Roles</p>
                </a>
            </li>

            <li class="nav-item" v-if="$root.can('asignar_permisos')">
                <a href="#asignar-permisos" class="nav-link">
                    <i class="fa fa-circle-o nav-icon"></i>
                    <p>Asignar Permisos</p>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "configuracion-menu"
    }
</script>

<style scoped>
    .sidebar-form, .nav-sidebar > .nav-header {
        padding: 1rem 0.5rem 0.5rem 1rem;
    }
</style>