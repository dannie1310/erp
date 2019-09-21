<template>
    <div class="btn-group">
        <EliminarSalida v-bind:id="value.id" v-bind:pagina="value.pagina"/>
    </div>
</template>

<script>
    import EliminarSalida from "../delete";

    export default {
        name: "action-buttons",
        components: {EliminarSalida},
        props: ['value']
    }

</script>