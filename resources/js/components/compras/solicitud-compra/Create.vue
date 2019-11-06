<template>
        <span>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <div class="row">
                            <div class="col-12">
                                <h4>
                                    <i class="fa fa-list"></i> Solicitud de Compra
                                </h4>
                            </div>
                        </div>
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row justify-content-between">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                             <label for="dptoResponsable">Dpto.Responsable</label>
                                            <CtgAreaCompradoraSelect
                                                name="id_area_compradora"
                                                data-vv-as="Dpto.Responsable"
                                                v-model="id_area_compradora"
                                                v-validate="{required: true}"
                                                :error="errors.has('id_area_compradora')"
                                                id="id_area_compradora"
                                                ref="CtgAreaCompradoraSelect"
                                                scope="UsuarioBelongs"
                                                />
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_area_compradora')">{{ errors.first('id_area_compradora') }}</div>
                                        </div>

                                    </div>


                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tipo">Tipo</label>
                                        <ctg-tipo-select
                                            data-vv-as="Tipo"
                                            id="id_tipo"
                                            name="id_tipo"
                                            :error="errors.has('id_tipo')"
                                            v-validate="{required: true}"
                                            v-model="id_tipo"/>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_tipo')">{{ errors.first('id_tipo') }}</div>
                                        </div>
                                    </div>

                                          <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="areaSolicitante">Área Solicitante</label>
                                            <CtgAreaSolicitanteSelect
                                                id="id_area_solicitante"
                                                data-vv-as="Área Solicitante"
                                                name="id_area_solicitante"
                                                v-model="id_area_solicitante"
                                                v-validate="{required: true}"
                                                :error="errors.has('id_area_solicitante')"
                                                scope="UsuarioBelongs"
                                            />
                                             <div style="display:block" class="invalid-feedback" v-show="errors.has('id_area_solicitante')">{{ errors.first('id_area_solicitante') }}</div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                        <label for="fechaRequisicion">Fecha Req. Origen</label>
                                           <input type="date"
                                                  name="fecha_requisicion"
                                                  v-model="fecha_requisicion"
                                                  id="fecha_requisicion"
                                                  class="form-control"
                                                  data-vv-as="Fecha">
                                        </div>

                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                        <label for="exampleInputEmail1">Folio Req. Origen</label>
                                        <input type="number"
                                               class="form-control"
                                               id="exampleInputEmail1"
                                               v-model="folio_requisicion"
                                               aria-describedby="folioRequisicion"
                                               placeholder="Folio Req. Origen">
                                      </div>
                                    </div>


                                    <div class="col-md-12">
                                       <div class="form-group">
                                           <label for="concepto">Concepto</label>
                                           <textarea class="form-control"
                                                     id="concepto"
                                                     name="concepto"
                                                     v-model="concepto"
                                                     rows="3"/>
                                       </div>
                                    </div>


                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="observaciones">Observaciones</label>
                                            <textarea class="form-control"
                                                      id="observaciones"
                                                      name="observaciones"
                                                      v-model="observaciones"
                                                      rows="3"/>

                                        </div>
                                        <hr>
                                    </div>

                                </div>

                            </div>
<!--                            <div class="container">-->
                                    <div class="d-flex flex-row-reverse">
                                        <div class="p-2">
                                            <button type="button" class="btn btn-primary" v-if="" @click="addRow(index)"> + Agregar Partida</button>
                                        </div>
                                    </div>
<!--                                </div>-->

                            <!--Partidas-->
                            <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th>No. de Parte</th>
                                                    <th>Marca</th>
                                                    <th>Modelo</th>
                                                    <th>Cantidad</th>
                                                    <th>Unidad</th>
                                                    <th>Fecha Requerida</th>
                                                    <th>Destino</th>
                                                    <th></th>
                                                    <th>Observaciones</th>
                                                    <th></th>
                                                </tr>
                                            </thead>


                                            <tbody >
                                                <tr v-for="(item, i) in items">
                                                    <td style="width: 260px;">
                                                        <MaterialSelect
                                                            scope="insumos".

                                                            :name="`material[${i}]`"
                                                            v-model="item.material"
                                                            data-vv-as="Material"
                                                            v-validate="{required: true}"
                                                            ref="MaterialSelect"
                                                            :disableBranchNodes="false"
                                                            :error="errors.has(`material[${i}]`)"
                                                            @input="setRowValues(item.material,i)"
                                                            :clase="`${item.material}`"/>

                                                        <div class="invalid-feedback" v-show="errors.has(`material[${i}]`)">{{ errors.first(`material[${i}]`) }}</div>
                                                    </td>

                                                    <td style="width: 100px;">
                                                        <input class="form-control"

                                                               v-model="item.numero_parte"
                                                               disabled/>
<!--                                                         <div class="invalid-feedback" v-show="errors.has(`numero_parte[${i}]`)">{{ errors.first(`numero_parte[${i}]`) }}</div>-->
                                                    </td>

                                                    <td>
                                                        <marca-select
                                                            :name="`marca[${i}]`"
                                                            v-model="item.marca"
                                                            data-vv-as="Marca"
                                                            v-validate="{required: true}"
                                                            :error="errors.has( `marca[${i}]`)"
                                                            />
                                                        <div style="display:block" class="invalid-feedback" v-show="errors.has(`marca[${i}]`)">{{ errors.first(`marca[${i}]`) }}</div>

                                                    </td>


                                                    <td>
                                                        <modelo-select
                                                            :name="`modelo[${i}]`"
                                                            v-model="item.modelo"
                                                            data-vv-as="Modelo"
                                                            v-validate="{required: true}"
                                                            :error="errors.has( `modelo[${i}]`)"
                                                             />
                                                          <div style="display:block" class="invalid-feedback" v-show="errors.has(`modelo[${i}]`)">{{ errors.first(`modelo[${i}]`) }}</div>
                                                    </td>

                                                    <td>
                                                        <input type="number"
                                                               class="form-control"
                                                               :name="`cantidad[${i}]`"
                                                               data-vv-as="Cantidad"
                                                               v-validate="{required: true}"
                                                               :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                               v-model="item.cantidad"/>
                                                         <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>

                                                    </td>


                                                    <td style="width:80px;">
                                                        <input class="form-control"

                                                               v-model="item.unidad" disabled/>
<!--                                                         <div style="display:block" class="invalid-feedback" v-show="errors.has(`unidad[${i}]`)">{{ errors.first(`unidad[${i}]`) }}</div>-->
                                                    </td>

                                                    <td>
                                                        <input type="date"
                                                               :name="`fecha[${i}]`"
                                                               class="form-control"
                                                               data-vv-as="Fecha"
                                                               v-validate="{required: true}"
                                                               :class="{'is-invalid': errors.has(`fecha[${i}]`)}"
                                                               v-model="item.fecha">
                                                        <div class="invalid-feedback" v-show="errors.has(`fecha[${i}]`)">{{ errors.first(`fecha[${i}]`) }}</div>
                                                    </td>

                                                    <td>
                                                        <input class="form-control"
                                                               :name="`destino[${i}]`"
                                                               data-vv-as="Destino"
                                                               v-validate="{required: true}"
                                                               :class="{'is-invalid': errors.has(`destino[${i}]`)}"
                                                               v-model="item.destino"/>

                                                        <input class="form-control"
                                                               :name="`id_destino[${i}]`"
                                                               data-vv-as="Destino"
                                                               v-validate="{required: true}"
                                                               :class="{'is-invalid': errors.has(`destino[${i}]`)}"
                                                               v-model="item.id_destino" hidden>

                                                        <div class="invalid-feedback" v-show="errors.has(`destino[${i}]`)">{{ errors.first(`destino[${i}]`) }}</div>
                                                    </td>

                                                    <td>

                                                        <SelectDestino
                                                            :name="`destino_id[${i}]`"
                                                            v-model="item.aux"
                                                            @input="setDestinoValues(item.aux, i)"
                                                        />
                                                    </td>

                                                    <td style="width: 160px;">
                                                        <textarea class="form-control"
                                                                  :name="`observaciones[${i}]`"
                                                                  data-vv-as="Observaciones"
                                                                  v-validate="{required: true}"
                                                                  :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                  v-model="item.observaciones"/>
                                                         <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                                    </td>

                                                    <td> <button type="button" class="btn btn-sm btn-outline-danger" v-if="" @click="removeRow(i)" title="Eliminar Partida"> <i class="fa fa-trash"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>

                                </div>
                            </div>
                            </div>







                              <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="salir">
                                <span v-if="cargando">
                                    <i class="fa fa-spin fa-spinner"></i>
                                </span>
                                <span v-else>
                                    Cerrar
                                </span>
                            </button>
                            <button type="submit" class="btn btn-primary" >Registrar</button>
                        </div>

                        </form>


                    </div>


                </div>
            </div>
    </span>
</template>

<script>
    import MaterialSelect from "../../cadeco/material/SelectAutocomplete"
    import CtgTipoSelect from "../../seguridad/compras/CtgTipoSelect";
    import SelectDestino from "../SelectDestino";
    import CtgAreaSolicitanteSelect from "../../seguridad/compras/CtgAreaSolicitanteSelect";
    import CtgAreaCompradoraSelect from "../../seguridad/compras/CtgAreaCompradoraSelect";
    import MarcaSelect from "../../sci/MarcaSelect";
    import ModeloSelect from "../../sci/ModeloSelect";

    export default {
        name: "solicitud-compra-create",
        components: {MaterialSelect, ModeloSelect, MarcaSelect, CtgAreaCompradoraSelect, CtgAreaSolicitanteSelect, SelectDestino, CtgTipoSelect},
        data(){
            return{
                cargando: false,
                index:0,
                id_area_compradora: '',
                id_area_solicitante: '',
                id_tipo: '',
                fecha_requisicion: '',
                folio_requisicion: '',
                concepto: '',
                observaciones: '',
                destino: '',
                items: [
                    {
                        aux:'',
                        material: "",
                        id_material:"",
                        numero_parte: "",
                        marca: "",
                        modelo: "",
                        cantidad:"",
                        unidad: "",
                        fecha:"",
                        destino:"",
                        id_destino:"",
                        observaciones:"",
                        destino_concepto: null,
                        destino_almacen: null,
                    }
                ],
            }

        },
        mounted() {
            this.$validator.reset()
        },
        methods : {
            addRow(index){
                    this.items.splice(index + 1, 0, {});
                    this.index = index+1;
            },
            removeRow(index){
                this.items.splice(index, 1);
            },
            salir(){
                this.$router.push({name: 'solicitud-compra'});
            },
            store() {
                return this.$store.dispatch('compras/solicitud-compra/store',  this.$data )
                    .then((data) => {
                        $(this.$refs.modal).modal('hide');
                        this.$emit('created',data)

                    })

            },
            setRowValues(material,i){
                this.items[i].id_material = material.id;
                this.items[i].numero_parte = material.numero_parte;
                this.items[i].unidad = material.unidad;
                this.items[i].material_status = true;
            },
            setDestinoValues(dest, i){

                if(dest.text){
                    /*Almacen*/
                    this.items[i].destino_concepto=false;
                    this.items[i].destino_almacen=true;
                    this.items[i].id_destino = dest.value;
                    this.items[i].destino = dest.text;
                }
                if(dest.path){
                   /*Concepto*/
                    this.items[i].destino_concepto=true;
                    this.items[i].destino_almacen=false;
                    this.items[i].id_destino = dest.id;
                    this.items[i].destino = dest.path;

                }

            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },

        },

    }
</script>

<style>
    .error > .vue-treeselect__control{
        border-color: #dc3545
    }

</style>
