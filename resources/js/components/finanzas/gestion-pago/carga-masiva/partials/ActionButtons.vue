<template>
    <div class="btn-group " v-if="value.id">
        <button @click="show()" type="button" class="btn btn-sm btn-outline-secondary" title="Ver Fondo">
            <i class="fa fa-eye"></i>
        </button>
    </div>


</template>

<script>


    export default {

        name: "action-buttons",
        components: { },
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
