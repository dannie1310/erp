<template>
    <span id="configuracion-seguridad-personalizado">
        <asignacion-roles-personalizado></asignacion-roles-personalizado>
        <desasignacion-roles-personalizado></desasignacion-roles-personalizado>
        <seguridad-rol-create-personalizado></seguridad-rol-create-personalizado>
        <asignacion-permisos-personalizado></asignacion-permisos-personalizado>
    </span>
    
</template>

<script>
    import AsignacionRolesPersonalizado from "./asignacion-roles/Personalizado";
    import DesasignacionRolesPersonalizado from "./desasignacion-roles/Personalizado";
    import SeguridadRolCreatePersonalizado from "../../../seguridad/personalizado/rol/Create";
    import AsignacionPermisosPersonalizado from "./asignacion-permisos/Personalizado";
    export default {
        name: "configuracion-seguridad-personalizado",
        components: {
            AsignacionPermisosPersonalizado,
            SeguridadRolCreatePersonalizado, DesasignacionRolesPersonalizado, AsignacionRolesPersonalizado}
    }
</script>