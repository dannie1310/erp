<template>
    <span id="configuracion-seguridad-personalizado">
        <asignacion-roles-personalizado ref="asignacion" v-if="$root.can('asignar_roles')"></asignacion-roles-personalizado>
        <desasignacion-roles-personalizado ref="desasignacion" v-if="$root.can('asignar_roles')"></desasignacion-roles-personalizado>
        <seguridad-rol-create-personalizado @creado="addRol($event)" v-if="$root.can('registrar_roles')"></seguridad-rol-create-personalizado>
        <asignacion-permisos-personalizado v-if="$root.can('asignar_permisos')"></asignacion-permisos-personalizado>
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
            SeguridadRolCreatePersonalizado, DesasignacionRolesPersonalizado, AsignacionRolesPersonalizado},
        methods:{
            addRol(data) {
                this.$refs.asignacion.addRol(data);
                this.$refs.desasignacion.addRol(data);
            }

        }
    }
</script>