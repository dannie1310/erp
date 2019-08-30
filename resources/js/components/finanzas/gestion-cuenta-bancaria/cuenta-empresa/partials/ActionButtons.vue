<template>
    <div class="btn-group">
        <Show v-if="value.show" v-bind:id="value.id" />
    </div>
</template>

<script>
    import Show from "../Show";
    export default {
        name: "action-buttons",
        components: {Show},
        props: ['value'],
        methods: {
            show() {
                this.$router.push({name: 'cuenta-empresa-show', params: {id: this.value.id}});
            },
        }
    }
</script>