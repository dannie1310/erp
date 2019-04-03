<template>
    <span>
        <asignacion-roles-masiva></asignacion-roles-masiva>
        <desasignacion-roles-masiva></desasignacion-roles-masiva>
    </span>
</template>

<script>
    import AsignacionRolesMasiva from "./asignacion-roles/Masiva";
    import DesasignacionRolesMasiva from "./desasignacion-roles/Masiva";
    export default {
        name: "configuracion-seguridad",
        components: {DesasignacionRolesMasiva, AsignacionRolesMasiva}
    }
</script>