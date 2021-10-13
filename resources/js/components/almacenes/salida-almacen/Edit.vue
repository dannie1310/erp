<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-info" :disabled="cargando">
            <i class="fa fa-pencil" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content" >
                    <div class="modal-header" v-if="salida">
                        <h5 class="modal-title" id="exampleModalLongTitle" v-if="salida.opciones == 1"> <i class="fa fa-sign-out"></i>SALIDA DE  ALMACÉN</h5>
                        <h5 class="modal-title" id="exampleModalLongTitle" v-else> <i class="fa fa-th"></i>TRANSFERENCIA DE ALMACÉN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="cargando">
                        <div>
                            <h5  id="exampleModalLongTitle"><i class="fa fa-spin fa-spinner"></i>CARGANDO</h5>
                        </div>
                    </div>
                    <form role="form" v-if="salida" @submit.prevent="validate">
                        <div class="modal-body" v-if="!cargando">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b v-if="salida.opciones == 1">Datos del Consumo</b>
                                                <b v-else>Datos de Transferencia</b>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="table-responsive col-md-12">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <td class="bg-gray-light"><b>Folio:</b></td>
                                                            <td class="bg-gray-light">{{salida.folio_format}}</td>
                                                            <td class="bg-gray-light"><b>Fecha:</b></td>
                                                            <td class="bg-gray-light">{{salida.fecha_format}}</td>

                                                        </tr>
                                                        <tr>
                                                            <td class="bg-gray-light"><b>Referencia:</b></td>
                                                            <td class="bg-gray-light">{{salida.referencia}}</td>
                                                            <td class="bg-gray-light"><b>Almacén:</b></td>
                                                            <td class="bg-gray-light">{{salida.almacen_descripcion}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Entrega a contratista-->
                                <div class="col-md-12">
                                        <div class="invoice p-3 mb-3">
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <b>Entrega a contratista</b>
                                                </div>
                                            </div>
                                            <div class="row col-md-12">
                                                <div class="table-responsive col-md-12">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:5%px">
                                                                    <div class="form-group error-content">
                                                                        <div class="input-group-text">
                                                                            <input type="checkbox" aria-label="Checkbox for following text input" class="icono" v-model="con_prestamo">
                                                                        </div>
                                                                    </div>
                                                                </th>
                                                                <th v-if="con_prestamo"  style="width:70%">
                                                                    <div class="form-group error-content">
                                                                        <model-list-select
                                                                            name="id_empresa"
                                                                            :disabled="!con_prestamo"
                                                                            placeholder="Seleccionar o buscar por RFC y razón social del contratista"
                                                                            data-vv-as="Empresa"
                                                                            v-validate="{required: true}"
                                                                            v-model="id_empresa"
                                                                            option-value="id"
                                                                            :custom-text="rfcAndRazonSocial"
                                                                            :list="contratistas"
                                                                            :isError="errors.has(`id_empresa`)">
                                                                        </model-list-select>
                                                                    </div>
                                                                </th>
                                                                <th v-else style="width:70%"></th>
                                                                <th v-if="con_prestamo"  style="width:25%">
                                                                     <div class="form-group error-content">
                                                                        <div class="form-group">
                                                                            <div class="btn-group btn-group-toggle">
                                                                                <label class="btn btn-outline-primary" :class="tipo_cargo === Number(key) ? 'active': ''" v-for="(cargo, key) in cargos" :key="key">
                                                                                    <input type="radio"
                                                                                        :disabled="!con_prestamo"
                                                                                        class="btn-group-toggle "
                                                                                        name="tipo_cargo"
                                                                                        :id="'opcion_cargo' + key"
                                                                                        :value="key"
                                                                                        autocomplete="on"
                                                                                        v-validate="{required: true}"
                                                                                        v-model.number="tipo_cargo">
                                                                                    {{ cargo }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </th>
                                                                <th v-else style="width:25%"></th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <!--Partidas-->
                                <div class="col-md-12">
                                    <div class="invoice p-3 mb-3">
                                        <div role="form">
                                            <div class="form-group row">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <b>Detalle de las partidas</b>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="table-responsive col-md-12">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th class="th_index">#</th>
                                                                    <th class="th_c120">No. de Parte</th>
                                                                    <th>Material</th>
                                                                    <th class="th_unidad">Unidad</th>
                                                                    <th class="th_cantidad">Cantidad</th>
                                                                    <th class="th_cantidad">Destino</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(partida, i) in salida.partidas.data">
                                                                    <td>{{i+1}}</td>
                                                                    <td>{{partida.material_numero_parte}}</td>
                                                                    <td>{{partida.material_descripcion}}</td>
                                                                    <td>{{partida.unidad}}</td>
                                                                    <td>{{partida.cantidad_format}}</td>
                                                                    <td v-if="partida.destino_path" :title="`${partida.destino_path}`"><u>{{partida.destino_descripcion}}</u></td>
                                                                    <td v-else >{{partida.destino_descripcion}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="col-md-2">
                                                        <b>Observaciones:</b>
                                                    </div>
                                                    <div class="col-md-10">
                                                        {{salida.observaciones}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" >Guardar Cambios</button>
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
        name: "salida-almacen-edit",
        components: {ModelListSelect},
        props: ['id','pagina'],
        data() {
            return {
                cargando: false,
                cargos: {
                    1: "Con Cargo",
                    0: "A Consignación"
                },
                con_prestamo : false,
                contratistas : [],
                salida: '',
                tipo_cargo: 1,
                id_empresa:'',
                contratista: 0,
                res:
                    {
                        tipo_cargo: '',
                        id_empresa:'',
                        contratista: ''
                    }

            }
        },
        methods: {
            init() {
                this.con_prestamo = false;
                this.contratistas = [];
                this.tipo_cargo = 1;
                this.id_empresa = '';
                this.contratista = 0;
                this.salida = '';
                this.res = {
                    tipo_cargo: '',
                    id_empresa: '',
                    contratista: ''
                }
            },
            rfcAndRazonSocial (item){
                return `[${item.rfc}] - ${item.razon_social}`
            },
            find() {
                this.init()
                this.cargando = true;
                this.getContratista();
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');

                this.$store.commit('almacenes/salida-almacen/SET_SALIDA', null);
                return this.$store.dispatch('almacenes/salida-almacen/find', {
                    id: this.id,
                    params: {include: ['partidas', 'entrega_contratista']}
                }).then(data => {
                    this.salida = data;

                    if(data.entrega_contratista)
                    {
                        this.con_prestamo = true;
                        this.$store.commit('almacenes/salida-almacen/SET_SALIDA', data);
                        this.id_empresa = this.salida.entrega_contratista.id_empresa;
                        this.tipo_cargo = this.salida.entrega_contratista.tipo_cargo;
                    }else if(this.salida.id_empresa_entrega){
                        this.con_prestamo = true;
                        this.id_empresa = parseInt(this.salida.id_empresa_entrega);
                        this.tipo_cargo = parseInt(this.salida.tipo_cargo);
                    }

                }).finally(() => {
                    this.cargando = false;
                })
            },
            getContratista() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'Contratista' }
                })
                    .then(data => {
                        this.contratistas = data.data;
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },
            update()
            {
                this.res.id_empresa = this.id_empresa;
                this.res.tipo_cargo = this.tipo_cargo;
                this.res.contratista = this.con_prestamo;
                 return this.$store.dispatch('almacenes/salida-almacen/actualizarEntrega', {
                       id: this.id,
                       params: this.res
                   })
                   .then(() => {
                       $(this.$refs.modal).modal('hide');
                   })

            }
        },
    }
</script>

<style scoped>

</style>
