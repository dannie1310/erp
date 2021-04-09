<template>
    <span>
        <div class="row">
            <div class="col-12">
                <button @click="find" class="btn btn-sm btn-outline-info" title="Editar">
                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                    <i class="fa fa-pencil" v-else></i>
                </button>
            </div>
        </div>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-factura"> <i class="fa fa-pencil"></i> EDITAR FACTURA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="!factura">
                            <div class="row" >
                                <div class="col-md-12">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Folio: {{factura.referencia}}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Fecha:</label>
                                        <div class="col-md-2">
                                            {{factura.fecha_cr}}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Empresa:</label>
                                        <div class="col-md-10">
                                            {{factura.empresa}}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Rubro de Factura:</label>
                                        <div class="col-md-10">
                                            <model-list-select
                                                name="id_rubro"
                                                placeholder="Seleccionar o buscar"
                                                data-vv-as="Rubro"
                                                v-validate="{required: true}"
                                                v-model="id_rubro"
                                                option-value="id"
                                                option-text="descripcion"
                                                :list="rubros"
                                                :isError="errors.has('id_rubro')">
                                            </model-list-select>
                                            <div class="invalid-feedback" v-show="errors.has('id_rubro')">{{ errors.first('id_rubro') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" v-on:click="salir">
                                        <i class="fa fa-angle-left"></i>
                                        Regresar
                                    </button>
                                    <button type="button" class="btn btn-primary" v-if="factura" v-on:click="update">
                                        <i class="fa fa-save"></i>
                                        Guardar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "Edit",
        props: ['id'],
        components: {ModelListSelect},
        data() {
            return {
                cargando : false,
                id_rubro : 0,
                rubros : [],
            }
        },
        methods: {
            find() {
                this.cargando = true;
                this.getRubros();
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show')
                this.$store.commit('finanzas/factura/SET_FACTURA', null);
                return this.$store.dispatch('finanzas/factura/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('finanzas/factura/SET_FACTURA', data);
                    this.id_rubro = data.id_rubro
                }).finally(() => {
                    this.cargando = false;
                })
            },
            getRubros() {
                return this.$store.dispatch('finanzas/rubro/index', {
                    params: {sort: 'descripcion', order: 'asc', scope:'paraFactura' }
                })
                    .then(data => {
                        this.rubros = data.data;
                    })
            },
            salir() {
                $(this.$refs.modal).modal('hide')
            },
            update() {
                return this.$store.dispatch('finanzas/factura/update',
                    {
                        id: this.id,
                        data: {
                            'rubro' : this.id_rubro
                        }
                    })
                    .then((data) => {
                        this.salir()
                    });
            }
        },
        computed: {
            factura() {
                return this.$store.getters['finanzas/factura/currentFactura']
            }
        }
    }
</script>

<style scoped>

</style>
