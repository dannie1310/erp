<template>
   <span>
        <button @click="init" v-if="$root.can('registrar_salida_almacen')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Crear Salida de Almacén
        </button>
       <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
           <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Registrar Salida de Almacén</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <!--Almacen-->
                                 <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="id_almacen">Almacen</label>
                                               <Almacen
                                                       name="id_almacen"
                                                       id="id_almacen"
                                                       data-vv-as="Número de Marbete"
                                                       v-validate="{required: true}"
                                                       v-model="dato.id_almacen"
                                                       :class="{'is-invalid': errors.has('id_almacen')}"
                                               ></Almacen>
                                          <div class="invalid-feedback" v-show="errors.has('id_almacen')">{{ errors.first('id_almacen') }}</div>
                                    </div>
                                 </div>
                            </div>
                            <div class="row">
                                <!--Material-->
                                <div class="col-md-12" v-if="empresas">
                                    <div class="form-group error-content">
                                        <label for="id_empresa">Contratista/Destajista solicitante del material:</label>
                                           <select
                                               class="form-control"
                                               name="id_empresa"
                                               data-vv-as="Empresa"
                                               v-model="dato.id_empresa"
                                               v-validate="{required: true}"
                                               id="id_empresa"
                                               :class="{'is-invalid': errors.has('id_empresa')}">
                                            <option value>-- Seleccione una Empresa --</option>
                                            <option v-for="(empresa, index) in empresas" :value="empresa.id"
                                                data-toggle="tooltip" data-placement="left" :title="empresa.razon_social ">
                                                {{ empresa.razon_social }}
                                            </option>
                                        </select>
                                         <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="id_tipo" class="col-sm-3 col-form-label">Tipo: </label>
                                        <div class="col-sm-10">
                                            <div class="btn-group btn-group-toggle">
                                                <label class="btn btn-outline-secondary" :class="id_tipo === Number(llave) ? 'active': ''" v-for="(tipo, llave) in tipos" :key="llave">
                                                    <input type="radio"
                                                       class="btn-group-toggle"
                                                       name="id_tipo"
                                                       data-vv-as="Tipo"
                                                       v-validate="{required: true}"
                                                       :id="'tipo' + llave"
                                                       :value="llave"
                                                       autocomplete="on"
                                                       v-model.number="id_tipo">
                                                        {{tipo}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
       </div>
   </span>
</template>

<script>
    import Almacen from "../../cadeco/almacen/Select";
    export default {
        name: "salida-almacen-create",
        components: {Almacen},
        data() {
            return {
                dato:{
                    id_almacen:'',
                    id_empresa:''
                },
                tipos: {
                    1: "Consumo",
                    2: "Transferencia"
                },
                empresas:[],
                cargando: false

            }
        },
        mounted() {
            this.getEmpresas()
        },
        methods: {
            init() {
                $(this.$refs.modal).modal('show');
                this.$validator.reset()
            },
            getEmpresas() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'TipoContratista' }
                })
                    .then(data => {
                        this.empresas = data.data;
                    })

            },
        }
    }
</script>