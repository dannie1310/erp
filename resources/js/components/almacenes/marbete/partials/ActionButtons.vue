<template>
    <div class="btn-group">
        <button @click="eliminar"  v-if="$root.can('eliminar_marbetes_manualmente')" type="button" class="btn btn-sm btn-outline-danger" title="Borrar Marbete"><i class="fa fa-trash"></i></button>
    </div>
</template>


<script>
    export default {
        name:"action-buttons",
        props: ['value'],
        methods:{
            eliminar(){
                this.cargando = true;
                return this.$store.dispatch('almacenes/marbete/eliminar', {
                    id: this.value.id,
                    params: { id_inventario_fisico: this.value.id_inventario_fisico}
                })
                    .then(data => {
                        this.$store.commit('almacenes/marbete/DELETE_MARBETE', {id: this.value.id})

                        this.$store.dispatch('almacenes/marbete/paginate', {
                            id: this.id,
                            params:{include:['almacen','material','inventario_fisico'], order:'desc', sort:'folio'}
                        })
                            .then(data => {
                                this.$store.commit('almacenes/marbete/SET_MARBETES', data.data);
                                this.$store.commit('almacenes/marbete/SET_META', data.meta);
                            })

                    })
                    .finally(() => {
                        this.cargando = false;
                    })

            },

        },


    }
</script>
