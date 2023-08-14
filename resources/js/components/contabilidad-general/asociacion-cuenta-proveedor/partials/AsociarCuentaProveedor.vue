<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Asociar">
            <i class="fas fa-exchange-alt"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-exchange-alt"></i> Asociación de Cuenta Contable Con Proveedor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <h6><b>Cuenta:</b></h6>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <h6>{{nombre}}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <h6><b>Asociar al proveedor:</b></h6>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <treeselect
                                            :class="{error: errors.has('proveedor')}"
                                            :async="true"
                                            :load-options="loadOptions"
                                            v-model="id_proveedor"
                                            v-validate="{required: true}"
                                            data-vv-as="Proveedor"
                                            name="proveedor"
                                            loadingText="Cargando..."
                                            searchPromptText="Escriba para buscar..."
                                            noResultsText="Sin Resultados"
                                            placeholder="-- Buscar Proveedor -- "
                                        >
                                        </treeselect>
                                        <div class="invalid-feedback" v-show="errors.has('id_proveedor')">{{ errors.first('id_proveedor') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-close"></i>Cerrar</button>
                            <button type="submit" class="btn btn-primary" > <i class="fas fa-exchange-alt"></i> Asociar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "asociacion-cuenta-proveedor-show",
        props: ['id_empresa','id_cuenta', 'nombre'],
        data() {
            return {
                cargando: false,
                id_proveedor: null,
            }
        },
        methods: {
            find(){
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.asociar()
                    }
                });
            },
            asociar(){
                return this.$store.dispatch('contabilidadGeneral/cuenta/asociarCuenta',
                {
                    params: {
                        id_empresa_contpaq:this.id_empresa,
                        id_cuenta_contpaq:this.id_cuenta,
                        id_proveedor_sat:this.id_proveedor,
                    }
                })
                .then(data => {
                    this.$store.commit('contabilidadGeneral/cuenta/UPDATE_CUENTA', data);
                    $(this.$refs.modal).modal('hide');
                }).finally(() => {

                });
            },
            loadOptions({ action, searchQuery, callback }) {
                if(searchQuery.length >= 3) {
                    return this.$store.dispatch('contabilidadGeneral/proveedor-sat/index', {
                        params: {
                            search: searchQuery,
                            limit: 50,
                            sort: 'razon_social',
                            order: 'ASC'
                        }
                    })
                    .then(data => {
                        const options = data.data.map(i => ({
                            id: i.id,
                            label: i.rfc + ' - ' +i.razon_social
                        }))
                        callback(null, options)
                    })
                }
            },
        }
    }
</script>

<style>

</style>
