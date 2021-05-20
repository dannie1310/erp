<template>
    <span v-if="value">
        <router-link :to="{name: 'cuenta-saldo-negativo-detalle', params: {id: this.value.id}}" >
            <span style="text-decoration: underline; cursor: pointer">{{value.saldo}}</span>
        </router-link>
    </span>
</template>

<script>
export default {
    name: "RouterSaldo",
    props: ['value'],
    methods: {
        ir() {

        },
    }
}
</script>

<style scoped>

a {
    color: #212529;
}

</style>
