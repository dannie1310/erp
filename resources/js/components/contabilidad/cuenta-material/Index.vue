<template>
    <div class="row">
        <div class="col-12">
            <cuenta-material-create></cuenta-material-create>
        </div>
    </div>
</template>

<script>
    import CuentaMaterialCreate from "./Create";
    export default {
        name: "cuenta-material-index",
        components: {CuentaMaterialCreate}
    }
</script>
