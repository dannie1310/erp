<template>
    <div class="btn-group">
        <button @click="edit" type="button" class="btn btn-sm btn-outline-secondary"> <i class="fa fa-pencil"></i> </button>
    </div>
</template>
<script>
    export default {
        name: "solicitud-compra-buttons",
        props: ['value'],
        methods: {
            edit() {
                this.$router.push({name:'solicitud-compra-edit', params: { id: this.value.id }});
            }
        }
    }
</script>

<style scoped>

</style>
