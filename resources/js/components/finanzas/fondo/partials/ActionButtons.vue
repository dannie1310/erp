<template>
        <div class="btn-group " v-if="value.id">
            <FondoShow v-if="value.show" @click="value.id" v-bind:id="value.id" />
        </div>


</template>

<script>
    import FondoShow from "../Show";
    import FondoCreate from "../Create";

    export default {

        name: "action-buttons",
        components: { FondoCreate, FondoShow },
        props: ['value'],
        methods: {
            destroy() {

            },
            show() {
                this.$router.push({name: 'fondo-show', params: {id: this.value.id}});
            },
        }
    }
</script>