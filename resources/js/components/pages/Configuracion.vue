<template>
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
<!--                <subcontratantes></subcontratantes>-->
                <compradoras></compradoras>
<!--                <solicitantes></solicitantes>-->
        </div>
    </div>
</template>

<script>
    import Subcontratantes from "./partials/Subcontratantes";
    import Compradoras from "./partials/Compradoras";
    import Solicitantes from "./partials/Solicitantes";
    export default {
        name: "configuracion",
        components: {Solicitantes, Compradoras, Subcontratantes},
    }
</script>
