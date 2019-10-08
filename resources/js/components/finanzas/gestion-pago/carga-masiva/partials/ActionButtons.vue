<template>
    <div class="btn-group" v-if="value.id">
        <button @click="show()" type="button" class="btn btn-sm btn-outline-success" title="Autorizar" v-if="value.autorizar && $root.can('autorizar_carga_layout_pago')">
            <i class="fa fa-check"></i>
        </button>
        <button @click="ver" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>

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
            ver() {
                this.$router.push({name: 'pago-masivo-show', params: {id: this.value.id}});
            },
        }
    }
</script>
