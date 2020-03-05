<template>
    <div class="btn-group">
        <button @click="find(value.id, 1)" type="button" class="btn btn-sm btn-outline-secondary  " title="Ver" style="margin-left:5px">
            <i class="fa fa-eye" aria-hidden="true"></i>
        </button>  
        <button @click="find(value.id, 2)" type="button" class="btn btn-sm btn-outline-primary  " title="Editar" v-if="value.editar">
            <i class="fa fa-pencil" aria-hidden="true"></i>
        </button>
        <delete v-bind:id="value.id" v-if="value.eliminar"></delete>
        <!-- <button type="button" v-if="value.eliminar" class="btn btn-sm btn-outline-danger  " @click="eliminar" title="Eliminar">
            <i class="fa fa-trash" aria-hidden="true"></i>
        </button> -->
    </div>
</template>

<script>
    import Delete from '../Delete';
    export default {
        name: "action-buttons",
        components: {Delete},
        props: ['value'],
        methods: {
            find(id, tipo) {
                this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', null);
                return this.$store.dispatch('cadeco/proveedor-contratista/find', {
                    id: id,
                    params: {include: ['suministrados', 'sucursales']}
                }).then(data => {
                    data.opcion = tipo;
                    this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', data);
                })
            },
        },
    }
</script>

<style scoped>
button.ex1 {
  margin-right: 1px;
}
</style>
