<template>
    <div class="btn-group">
<!--
        <router-link  :to="{ name: 'orden-compra-edit', params: {id: value.id}}" v-if="$root.can('modificar_orden_compra') && !value.tiene_entradas" type="button" class="btn btn-sm btn-outline-success" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <Relaciones v-bind:transaccion="value.transaccion"/>
        <router-link  :to="{ name: 'orden-compra-documentos', params: {id: value.id}}" v-if="$root.can('consultar_orden_compra') && $root.can('consultar_archivos_transaccion')" type="button" class="btn btn-sm btn-outline-primary" title="Ver">
            <i class="fa fa-folder-open"></i>
        </router-link>-->
    </div>
</template>

<script>

    import Relaciones from "../../../globals/ModalRelaciones";
    export default {
        name: "invitacion-compra-action-buttons",
        components: {Relaciones},
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
