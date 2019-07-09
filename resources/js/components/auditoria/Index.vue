<template>
    <span>
         <div class="row">
            <div class="col-md-12">
                <permiso-table-obra></permiso-table-obra>
            </div>
        </div>
    </span>
</template>

<script>
    import PermisoTableObra from "./partials/PermisoTableObra";

    export default {
        name: "auditoria-index",
        components:{PermisoTableObra}
    }
</script>
