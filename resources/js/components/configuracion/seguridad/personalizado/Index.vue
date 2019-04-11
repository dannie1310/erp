<template>
    <span id="configuracion-seguridad-personalizado">
        <asignacion-roles-personalizado></asignacion-roles-personalizado>
        <desasignacion-roles-personalizado></desasignacion-roles-personalizado>
        <seguridad-rol-create-personalizado  v-if="$root.can('registrar-roles')"></seguridad-rol-create-personalizado>
    </span>
    
</template>

<script>
    import AsignacionRolesPersonalizado from "./asignacion-roles/Personalizado";
    import DesasignacionRolesPersonalizado from "./desasignacion-roles/Personalizado";
    import SeguridadRolCreatePersonalizado from "../../../seguridad/personalizado/rol/Create";
    export default {
        name: "configuracion-seguridad-personalizado",
        components: {SeguridadRolCreatePersonalizado, DesasignacionRolesPersonalizado, AsignacionRolesPersonalizado}
    }
</script>