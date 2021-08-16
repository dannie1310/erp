<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'cotizacion-proveedor-invitacion', params: {id_invitacion: value.id}}" v-if="value.registrar_cotizacion" type="button" class="btn btn-sm btn-outline-success" title="Cotizar">
            <i class="fa fa-comment-dollar"></i>
        </router-link>
        <router-link  :to="{ name: 'cotizacion-proveedor-edit', params: {id_invitacion: value.id}}" v-if="value.editar_cotizacion" type="button" class="btn btn-sm btn-outline-primary" title="Editar Cotización">
            <i class="fa fa-comment-dollar"></i>
        </router-link>
        <router-link  :to="{ name: 'invitacion-proveedor-show', params: {id: value.id}}" v-if="$root.can('consultar_invitacion_cotizar_proveedor', 1)" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
    </div>
</template>

<script>

    import Relaciones from "../../../globals/ModalRelaciones";
    export default {
        name: "invitacion-proveedor-action-buttons",
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
