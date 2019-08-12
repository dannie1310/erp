<template>
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
        <button type="button" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fa fa-pencil"></i></button>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        props: ['value'],
    }
</script>

<style scoped>

</style>
