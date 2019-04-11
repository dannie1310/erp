<template>
    <span id="configuracion-seguridad">
        <asignacion-roles-masiva></asignacion-roles-masiva>
        <desasignacion-roles-masiva></desasignacion-roles-masiva>
        <seguridad-rol-create v-if="$root.can('registrar-roles')"></seguridad-rol-create>
    </span>
</template>

<script>
    import SeguridadRolCreate from "../../../seguridad/global/rol/Create";
    import AsignacionRolesMasiva from "./asignacion-roles/Masiva";
    import DesasignacionRolesMasiva from "./desasignacion-roles/Masiva";
    export default {
        name: "configuracion-seguridad",
        components: {SeguridadRolCreate, DesasignacionRolesMasiva, AsignacionRolesMasiva}
    }
</script>