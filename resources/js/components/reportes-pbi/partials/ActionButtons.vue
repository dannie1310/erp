<template>
    <span>
        <div class="btn-group">
            <button @click="ver" type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
        </div>
    </span>
</template>

<script>

    export default {
        name: "action-buttons",
        props: ['value'],
        methods: {
            ver() {
                this.$router.push({name: 'visor-reportes', params: {id: this.value.id}});
            },
        }
    }
</script>

<style scoped>

</style>
