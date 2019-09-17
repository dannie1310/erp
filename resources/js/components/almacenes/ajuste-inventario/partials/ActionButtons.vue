<template>
    <div class="btn-group">
        <button @click="show" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
    </div>
</template>

<script>
    export default {
        name: "ActionButtons",
        props: ['value'],
    }
</script>

<style scoped>

</style>