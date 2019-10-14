<template>
    <div class="btn-group">
        <ShowSalida  v-bind:id="value.id" v-bind:pagina="value.pagina"></ShowSalida>
        <EliminarSalida v-bind:id="value.id" v-bind:pagina="value.pagina"/>
    </div>
</template>

<script>
    import EliminarSalida from "../delete";
    import ShowSalida from "../Show";

    export default {
        name: "action-buttons",
        components: {EliminarSalida, ShowSalida},
        props: ['value']
    }

</script>