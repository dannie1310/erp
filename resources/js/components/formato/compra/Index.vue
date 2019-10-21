<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form role="form" @submit.prevent="validate">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Datos de Consulta</h3>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="id_empresa">Proveedor</label>
                                        <select
                                                type="text"
                                                name="id_empresa"
                                                data-vv-as="Empresa"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_empresa"
                                                v-model="id_empresa"
                                                :class="{'is-invalid': errors.has('id_empresa')}"
                                        >
                                            <option value>-- Proveedor --</option>
                                            <option v-for="c in empresas" :value="c.id">{{ c.razon_social }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label for="id_subcontrato">Orden Compra</label>
                                        <select
                                                :disabled="!id_empresa"
                                                type="text"
                                                name="id_subcontrato"
                                                data-vv-as="Subcontrato"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_subcontrato"
                                                v-model="id_orden_compra"
                                                :class="{'is-invalid': errors.has('id_orden_compra')}"
                                        >
                                            <option value>-- Orden Compra --</option>
                                            <option v-for="c in ordenes_compras" :value="c.id">{{ c.numero_folio_format }} {{c.observaciones_format}}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_orden_compra')">{{ errors.first('id_orden_compra') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer btn-group" v-if="id_orden_compra">
                            <PDF v-bind:id="id_orden_compra" v-bind:id_empresa="id_empresa" @click="validate"></PDF>
                        </div>
                        <div class="modal-footer btn-group" v-else>
                            <button type="submit" class="btn btn-primary" @click="validate">Ver Formato</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</template>

<script>
    import PDF from "../../compras/orden-compra/partials/FormatoOrdenCompra";
    export default {
        name: "formato-orden-compra-index",
        components: {PDF},
        data() {
            return {
                cargando: false,
                id_empresa: '',
                id_orden_compra:  '',
                empresas: [],
                ordenes_compras: [],
                ok : false,
                pdf_datos: ""
            }
        },
        mounted() {
            this.getEmpresas();
        },
        computed: {
        },
        methods: {
            init() {
                this.cargando = true;
                $(this.$refs.modal).modal('show');

                this.id_empresa = '';
                this.empresas = '';

                this.$validator.reset()
                this.cargando = false;
            },
            getEmpresas() {
                this.id_empresa= '';
                this.id_orden_compra=  '';
                this.empresas= [];
                this.ordenes_compras= [];
                this.$store.commit('cadeco/empresa/SET_EMPRESAS', []);
                this.cargando = true;
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {scope: 'paraOrdenCompra'}
                })
                    .then(data => {
                        this.empresas = data.data
                        if(this.empresas.length){
                            $(this.$refs.modal).modal('show');
                        }
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getOrdenCompra() {
                this.id_orden_compra= '';
                this.ordenes_compras= [];
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: this.id_empresa,
                    params: { include: 'ordenes_compra' }
                }).then(data => {
                    this.ordenes_compras = data.ordenes_compra.data;
                    if(this.ordenes_compras.length){
                        $(this.$refs.modal).modal('show');

                    }
                })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {

                    }
                });
            }
        },
        watch: {
            id_empresa(value) {
                this.ordenes_compras = []
                if (value) {
                    this.getOrdenCompra();
                }
            }
        }
    }
</script>

<style scoped>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>