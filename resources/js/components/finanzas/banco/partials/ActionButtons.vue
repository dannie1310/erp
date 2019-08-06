<template>
    <div class="btn-group " v-if="value.id">
        <BancoShow v-if="value.show" @click="value.id" v-bind:id="value.id" />
    </div>


</template>

<script>
    import BancoShow from "../Show";
    import BancoCreate from "../Create";

    export default {

        name: "action-buttons",
        components: { BancoCreate, BancoShow },
        props: ['value'],
        methods: {
            destroy() {

            },
            show() {
               this.$router.push({name: 'banco-show', params: {id: this.value.id}});
            },
        }
    }
</script>
