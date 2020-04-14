<template>
    <div class="btn-group">
        <Aprobar v-if="value.aprobar" v-bind:id="value.id"></Aprobar>
        <!-- <button @click="edit" type="button" class="btn btn-sm btn-outline-info" title="Editar Solicitud"> <i class="fa fa-pencil"></i> </button>-->
        <SolicitudShow v-if="value.show" @click="value.id" v-bind:id="value.id"/>
       <!--  <button @click="eliminar" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar Solicitud"><i class="fa fa-trash"></i></button>
        <PDF  v-if="value.id" v-bind:id="value.id" @click="value.id"/> -->
    </div>
</template>
<script>
    import SolicitudShow from '../Show.vue';
    import PDF from '../FormatoSolicitudCompra.vue';
    import Aprobar from '../Autorizar';
    export default {
        name: "solicitud-compra-buttons",
        components: {PDF, SolicitudShow, Aprobar},
        props: ['value'],
        methods: {
            edit() {
                this.$router.push({name:'solicitud-compra-edit', params: { id: this.value.id }});
           },
            eliminar(){
                this.cargando = true;
                return this.$store.dispatch('compras/solicitud-compra/eliminar', {
                    id: this.value.id,
                })
                    .then(data => {
                        this.$store.commit('compras/solicitud-compra/DELETE_SOLICITUD', {id: this.value.id})

                        this.$store.dispatch('compras/solicitud-compra/paginate', {
                            id: this.id,
                            params:{include:['solicitud','empresa'], order:'desc', sort:'id_transaccion'}
                        })
                            .then(data => {
                                this.$store.commit('compras/solicitud-compra/SET_SOLICITUDES', data.data);
                                this.$store.commit('compras/solicitud-compra/SET_META', data.meta);
                            })

                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        }
    }
</script>

<style scoped>

</style>
