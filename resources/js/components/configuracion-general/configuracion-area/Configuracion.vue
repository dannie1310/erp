<template>
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
                <subcontratantes></subcontratantes>
                <compradoras></compradoras>
                <solicitantes></solicitantes>
        </div>
    </div>
</template>

<script>
    import Subcontratantes from "./Subcontratantes";
    import Compradoras from "./Compradoras";
    import Solicitantes from "./Solicitantes";
    export default {
        name: "configuracion-area",
        components: {Solicitantes, Compradoras, Subcontratantes},
    }
</script>
