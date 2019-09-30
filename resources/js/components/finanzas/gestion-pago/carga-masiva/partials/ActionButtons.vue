<template>
    <div class="btn-group" v-if="value.id">
        <button @click="show()" type="button" class="btn btn-sm btn-outline-success" title="Autorizar" v-if="value.autorizar">
            <i class="fa fa-check"></i>
        </button>
    </div>


</template>

<script>


    export default {

        name: "action-buttons",
        components: { },
        props: ['value'],
        data() {
            return {

            }
        },
        mounted() {

        },
        methods: {
            destroy() {

            },
            show() {
                this.$router.push({name: 'autorizar-layout', params: {id: this.value.id}});
            },
        }
    }
</script>
