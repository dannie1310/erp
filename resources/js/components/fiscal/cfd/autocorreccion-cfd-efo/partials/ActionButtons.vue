<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-if="value.show" />
        <Aplicar v-bind:id="value.id" v-if="value.aplicar"/>
    </div>
</template>

<script>
    import Aplicar from "../Aplicar";
    import Show from "../Show";
    export default {
        name: "ActionButtons",
        components:{Show, Aplicar},
        props: ['value']
    }
</script>

<style scoped>

</style>
