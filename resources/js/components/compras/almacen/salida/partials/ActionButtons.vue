<template>
    <div class="btn-group">
        <EliminarSalida v-bind:id="value.id"/>
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