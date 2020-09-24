<template>
    <div class="btn-group">
        <PDF v-bind:id="value.id" @click="value.id"></PDF>
        <button @click="editar" type="button"  :disabled="value.tiene_entradas" class="btn btn-sm btn-outline-success" v-if="$root.can('modificar_orden_compra') && !value.tiene_entradas" :title="value.tiene_entradas?'Orden con Entrada de Almacen':'Editar'"><i class="fa fa-pencil"></i></button>
        <Eliminar @created="paginate()" v-bind:id="value.id" v-if="$root.can('eliminar_orden_compra') && !value.tiene_entradas"></Eliminar>
    </div>
</template>

<script>

    import PDF from './FormatoOrdenCompra';
    import Eliminar from '../Delete';
    export default {
        name: "action-buttons",
        components: {PDF, Eliminar},
        props: ['value'],
        data(){
            return{
                query: {scope: ['areasCompradorasAsignadas'], include: ['solicitud','empresa'], sort: 'id_transaccion', order: 'desc'},
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
            paginate() {
                this.cargando = true;
                this.$store.commit('compras/orden-compra/SET_ORDENES', null);
                return this.$store.dispatch('compras/orden-compra/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('compras/orden-compra/SET_ORDENES', data.data);
                        this.$store.commit('compras/orden-compra/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        }
    }
</script>
