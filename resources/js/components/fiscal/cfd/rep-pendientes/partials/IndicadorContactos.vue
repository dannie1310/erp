<template>
    <span>
        <i class="fa fa-users" v-if="value==1"></i>

    </span>
</template>

<script>
export default {
    name: "IndicadorContatos.vue",
    props: ['value'],
    data() {
        return {
        }
    },
}
</script>

<style scoped>

</style>
