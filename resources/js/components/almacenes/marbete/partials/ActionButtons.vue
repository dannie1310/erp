<template>
    <div class="btn-group" v-if="value.id">
        <button @click="eliminar" type="button" class="btn btn-sm btn-outline-danger" title="Borrar Marbete"><i class="fa fa-trash"></i></button>
    </div>
</template>


<script>
    export default {
        name:"action-buttons",
        props: ['value'],
        data(){

        },
        mounted(){

        },
        methods:{
            eliminar(){
                this.cargando = true;
                return this.$store.dispatch('almacenes/marbete/eliminar', {
                    id: this.value.id,
                    params: {}
                })
                    .then(data => {
                        this.$store.commit('almacenes/marbete/DELETE_MARBETE', {id: this.value.id})

                        this.$store.dispatch('almacenes/marbete/paginate', {
                            id: this.id,
                            params:{ scope:'InventarioFisico:'+this.id, include:['almacen','material'], order:'desc', sort:'folio'}
                        })
                            .then(data => {
                                // console.log(data);
                                this.$store.commit('almacenes/marbete/SET_MARBETES', data.data);
                                this.$store.commit('almacenes/marbete/SET_META', data.meta);
                            })

                    })
                    .finally(() => {
                        this.cargando = false;
                    })

            },
            computed: {

            },
            watch:{

            }
        }
    }
</script>
