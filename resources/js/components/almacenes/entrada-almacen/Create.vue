<template>
     <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Registrar Entrada de Almacén
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="id_remesa">Orden de Compra: </label>
                                        <select
                                                type="text"
                                                name="id_orden_compra"
                                                data-vv-as="Orden de Compra"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_orden_compra"
                                                v-model="id_orden_compra"
                                                :class="{'is-invalid': errors.has('id_orden_compra')}"
                                        >
                                            <option value>-- Seleccione una Orden de Compra --</option>
                                            <option v-for="orden in ordenes_compra" :value="orden.id">{{ orden.numero_folio_format }} ({{ orden.dato_transaccion }})</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_orden_compra')">{{ errors.first('id_orden_compra') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
     </span>
</template>

<script>
    export default {
        name: "entrada-almacen-create",
        data() {
            return {
                id_orden_compra : '',
                ordenes_compra : [],
                cargando: false
            }
        },
        mounted() {
            this.getOrdenesCompra();
        },
        methods: {
            init() {
                this.cargando = true;
                this.id_orden_compra = '';
                this.ordenes_compra = [];
                this.cargando = false;
            },
            getOrdenesCompra() {
                return this.$store.dispatch('compras/orden-compra/index', {
                    config: {
                        params: {
                            scope: 'disponibleEntradaAlmacen',
                            sort: 'numero_folio',
                            order: 'desc'
                        }
                    }
                }).then(data => {
                    this.ordenes_compra = data;
                })
            },
        }
    }
</script>

<style scoped>

</style>