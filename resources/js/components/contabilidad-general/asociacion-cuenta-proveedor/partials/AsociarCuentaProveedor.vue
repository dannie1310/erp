<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Asociar">
            <i class="fas fa-exchange-alt"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" v-if="proveedoresSat">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-exchange-alt"></i> ASOCIACIÓN CUENTA PROVEEDOR</h5>
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
                                        <model-list-select
                                                :disabled="cargando"
                                                name="id_proveedor"
                                                :placeholder="!cargando?'Seleccionar o buscar por rfc o razón social':'Cargando...'"
                                                data-vv-as="ProveedorSat"
                                                v-model="id_proveedor"
                                                v-validate="{required: true}"
                                                option-value="id"
                                                :custom-text="rfcAndRazonSocial"
                                                :list="proveedoresSat"
                                                :isError="errors.has(`id_proveedor`)">
                                        </model-list-select>
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
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "asociacion-cuenta-proveedor-show",
        props: ['id_empresa','id_cuenta', 'nombre'],
        components: {ModelListSelect},
        data() {
            return {
                cargando: false,
                proveedoresSat:[],
                id_proveedor:'',
            }
        },
        methods: {
            rfcAndRazonSocial (item) {
                return `[${item.rfc}] - ${item.razon_social}`
            },
            find(){
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$Progress.start();
                return this.$store.dispatch('contabilidadGeneral/proveedor-sat/buscarProveedoresSat',
                {
                    params: {include: ['cuenta_contpaq_proveedor_sat'], nombre:this.nombre}
                })
                .then(data => {
                    this.proveedoresSat = data.data;
                }).finally(() => {
                    this.$Progress.finish();
                });
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

        }

    }
</script>

<style>

</style>