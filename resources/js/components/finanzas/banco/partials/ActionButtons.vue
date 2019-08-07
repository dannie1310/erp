<template>
    <div class="btn-group " v-if="value.id">
        <BancoShow v-if="value.show" @click="value.id" v-bind:id="value.id" />
        <BancoEdit  v-if="value.edit" v-bind:id="value.id" :key="value.id"/>
    </div>


</template>

<script>
    import BancoShow from "../Show";
    import BancoCreate from "../Create";
    import BancoEdit from "../Edit";

    export default {

        name: "action-buttons",
        components: { BancoCreate, BancoShow, BancoEdit },
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
