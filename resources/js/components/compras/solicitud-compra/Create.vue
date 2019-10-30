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
                        <form role="form">
                            <div class="modal-body">
                                <div class="row justify-content-between">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                             <label for="Ex">Dpto.Responsable</label>
                                            <CtgAreaCompradoraSelect></CtgAreaCompradoraSelect>
                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleF">Tipo</label>
                                        <ctg-tipo-select
                                            ref="CtgTipoSelect"
                                            id="id_tipo"
                                            name="id_tipo"
                                            v-model="id_tipo"/>
                                        </div>
                                    </div>

                                          <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="Ex">Área Responsable</label>
                                            <CtgAreaSolicitanteSelect v-model="id_area"></CtgAreaSolicitanteSelect>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                        <label for="exampleInputEmail1">Fecha Req. Origen</label>
                                           <input type="date" name="fecha" id="fecha" class="form-control" data-vv-as="Fecha">
                                        </div>

                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                        <label for="exampleInputEmail1">Folio Req. Origen</label>
                                        <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Folio Req. Origen">
                                      </div>
                                    </div>


                                    <div class="col-md-12">
                                       <div class="form-group">
                                           <label for="exampleFormControlTextarea1">Concepto</label>
                                           <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                                       </div>
                                    </div>


                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="exampleFormControlTextarea1">Observaciones</label>
                                            <textarea class="form-control" id="exampleFormControlTextarea2" rows="3"></textarea>
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
                                                <tr v-for="(row, i) in rows" v-bind:id="i">
                                                    <td style="width: 260px;"><MaterialSelect v-model="row.descripcion" @input="setRowValues(i)" /></td>
                                                    <td><input class="form-control" v-if="material" :value="material.numero_parte"/></td>
                                                    <td><marca-select></marca-select></td>
                                                    <td><modelo-select></modelo-select></td>
                                                    <td><input type="number" class="form-control" v-model="row.cantidad"/></td>
                                                    <td style="width:80px;"><input class="form-control" v-if="material" :value="material.unidad"/></td>
                                                    <td><input type="date" name="fecha" class="form-control" data-vv-as="Fecha" v-model="row.fecha"></td>
                                                    <td><input class="form-control" v-model="row.destino"/> </td>
                                                    <td><SelectDestino v-model="destino"></SelectDestino></td>
                                                    <td style="width: 160px;"><textarea class="form-control" v-model="row.observaciones"></textarea></td>
                                                    <td> <button type="button" class="btn btn-outline-danger" v-if="" @click="removeRow(i)" title="Eliminar Partida"> <i class="fa fa-trash"></i></button></td>
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
                            <button type="button" class="btn btn-primary" @click="registrar" >Registrar</button>
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
        components: {
            ModeloSelect,
            MarcaSelect,
            CtgAreaCompradoraSelect, MaterialSelect, CtgAreaSolicitanteSelect, SelectDestino, CtgTipoSelect},
        data(){
            return{
                cargando: false,
                index: 0,
                id_area:'',
                id_tipo:'',
                destino:'',
                unidad:'',
                arr:{},
                rows: [
                    {
                        descripcion: "",
                        numero_parte: "",
                        marca: "",
                        modelo: "",
                        cantidad:"",
                        unidad: "",
                        fecha:"",
                        destino:"",
                        observaciones:"",
                    }
                ],
            }

        },
        mounted() {

        },
        methods : {
            addRow(index){
                    this.rows.splice(index + 1, 0, {});
                    this.index = index+1;
            },
            removeRow(index){
                this.rows.splice(index, 1);
            },
            salir(){
                this.$router.push({name: 'solicitud-compra'});
            },
            registrar() {
                // console.log(this.id_tipo);
                // console.log(this.destino);

                // console.log(this.$refs.material.unidad);
                console.log(MaterialSelect.methods.getMaterialInfo());
                // console.log(MaterialSelect.data());
                console.log(this.unidad);
                // console.log(this.rows);

            },
            setRowValues(index){
                console.log(index);
                // console.log(material);
                // this.arr = this.$store.state.material();
                // console.log(this.arr);

               /*Aquí cambiamos los valores del arreglo de row dependiendo de la posición*/

            }

        },
        computed: {
            material() {


               return this.$store.getters['cadeco/material/currentMaterial']
            },
        }
    }
</script>

<style scoped>

</style>
