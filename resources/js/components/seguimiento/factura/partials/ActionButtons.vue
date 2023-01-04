<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'factura-seg-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <button @click="cancelar"  v-if="value.cancelar" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar"><i class="fa fa-ban"></i></button>
    </div>
</template>

<script>
    export default {
        name: "factura-action-buttons",
        components: {},
        props: ['value'],
        methods: {
            cancelar() {
                return this.$store.dispatch('seguimiento/factura/cancelar', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                        return this.$store.dispatch('seguimiento/factura/paginate', {
                            params: {include: [], sort: 'idfactura', order: 'desc'},
                        }).then(data => {
                            this.$store.commit('seguimiento/factura/SET_FACTURAS', data.data);
                            this.$store.commit('seguimiento/factura/SET_META', data.meta);
                        })
                    })
            },
        }
    }
</script>
