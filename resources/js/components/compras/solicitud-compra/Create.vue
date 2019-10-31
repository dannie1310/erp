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
                                            <label for="Ex">Área Solicitante</label>
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
                                                <tr v-for="(item, i) in items" v-bind:id="i">
                                                    <td style="width: 260px;"><MaterialSelect scope="insumos" v-model="item.material" @input="setRowValues(item.material,i)"/> </td>

                                                    <td style="width: 100px;"><input class="form-control"  v-model="item.numero_parte"   disabled/></td>

                                                    <td><marca-select v-model="item.marca"></marca-select></td>

                                                    <td><modelo-select v-model="item.modelo"></modelo-select></td>

                                                    <td><input type="number" class="form-control" v-model="item.cantidad"/></td>

                                                    <td style="width:80px;"><input class="form-control"  v-model="item.unidad" disabled/></td>

                                                    <td><input type="date" name="fecha" class="form-control" data-vv-as="Fecha" v-model="item.fecha"></td>

                                                    <td><input class="form-control" v-model="item.destino"/> </td>

                                                    <td><SelectDestino v-model="item.id_destino" @input="setDestinoValues(item.id_destino, i)"/></td>

                                                    <td style="width: 160px;"><textarea class="form-control" v-model="item.observaciones"></textarea></td>

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
                items: [
                    {
                        material: "",
                        numero_parte: "",
                        marca: "",
                        modelo: "",
                        cantidad:"",
                        unidad: "",
                        fecha:"",
                        destino:"",
                        id_destino:"",
                        observaciones:"",
                    }
                ],
            }

        },
        mounted() {

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
            registrar() {
                console.log(this.items);
            },
            setRowValues(material,i){
                this.items[i].numero_parte = material.numero_parte;
                this.items[i].unidad = material.unidad;
            },
            setDestinoValues(dest, i){

                if(dest.text){
                    /*Almacen*/
                    this.items[i].id_destino = dest.value;
                    this.items[i].destino = dest.text;
                }
                if(dest.path){
                   /*Concepto*/
                    this.items[i].id_destino = dest.id;
                    this.items[i].destino = dest.path;
                }

            }

        },

    }
</script>

<style scoped>

</style>
