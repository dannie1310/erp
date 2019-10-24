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

                                    <div class="col-md-4">Dpto.Responsable</div>
                                    <div class="col-md-4">Tipo</div>
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

                            <!--Partidas-->
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
                                                            <th>Observaciones</th>
                                                </tr>
                                            </thead>
                                        </table>

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
                            <button type="button" class="btn btn-primary" v-if="" @click="">Registrar</button>
                        </div>

                        </form>


                    </div>




<!--                               <form>-->
<!--                                   <div class="form-group">-->
<!--                                    <label for="exampleFormControlTextarea1">Concepto</label>-->
<!--                                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>-->
<!--                                  </div>-->

<!--                                   <div class="form-group">-->
                    <!--                                    <label for="exampleFormControlTextarea1">Observaciones</label>-->
                    <!--                                    <textarea class="form-control" id="exampleFormControlTextarea2" rows="3"></textarea>-->
                    <!--                                 </div>-->
<!--                        <hr>-->



<!--                                   &lt;!&ndash;Solicitud&ndash;&gt;-->

<!--                                   &lt;!&ndash;Partidas&ndash;&gt;-->

<!--                    </form>-->
<!--                    <button type="button" class="btn btn-primary">Guardar</button>-->
                </div>
            </div>
    </span>
</template>

<script>
    export default {
        name: "solicitud-compra-create"
    }
</script>

<style scoped>

</style>
