<template>
    <div>
        <i class="fa fa-times-circle" style="color: red" v-if="value==0"></i>
        <i class="fa fa-exclamation-triangle" style="color: orange" v-else-if="value==1"></i>
        <i class="fa fa-check-circle" style="color: green" v-else-if="value==2"></i>
    </div>
</template>

<script>
export default {
    name: "EstadoAcceso",
    props: ['value'],
    data() {
        return {
        }
    },

    methods: {

    },
}
</script>

<style scoped>

</style>
