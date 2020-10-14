<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-bind:show="value.show"></Show>
        <Relaciones v-bind:relaciones="value.relaciones"/>
    </div>
</template>

<script>
    import Show from '../Show'
    import Relaciones from "../../../globals/ModalRelaciones";
    export default {
        name: "ActionButtons",
        components: {Show, Relaciones},
        props: ['value'],
    }
</script>
