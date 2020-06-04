<template>
    <div class="btn-group">
        <PDF v-bind:id="value.id" @click="value.id"></PDF>
        <button @click="editar" type="button" :disabled="value.tiene_entradas" class="btn btn-sm btn-outline-success" :title="value.tiene_entradas?'Orden con Entrada de Almacen':'Eliminar'"><i class="fa fa-pencil"></i></button>
        <button @click="eliminar" type="button" :disabled="value.tiene_entradas" class="btn btn-sm btn-outline-danger" :title="value.tiene_entradas?'Orden con Entrada de Almacen':'Eliminar'"><i class="fa fa-trash"></i></button>
    </div>
</template>

<script>

    import PDF from './FormatoOrdenCompra';
    export default {
        name: "action-buttons",
        components: {PDF},
        props: ['value'],
        data(){
            return{
                query: {include: ['solicitud','empresa'], sort: 'id_transaccion', order: 'desc'},
            }
        },
        methods: {
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {

                    }
                });
            },
            eliminar(){
                return this.$store.dispatch('compras/orden-compra/eliminarOrdenes', { data:[this.value.id]}
                  ).then(data => {
                    this.$store.commit('compras/orden-compra/SET_ORDENES', []);  
                    return this.$store.dispatch('compras/orden-compra/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('compras/orden-compra/SET_ORDENES', data.data);
                        this.$store.commit('compras/orden-compra/SET_META', data.meta);
                    })
                })
            },
            editar(){
                this.$router.push({name: 'orden-compra-edit', params: { id: this.value.id }});
            },
        }
    }
</script>