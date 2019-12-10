<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-md-12" >
                    <div class="invoice p-3 mb-3">
                     <form role="form" @submit.prevent="validate">
                        <div class="body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <label for="fecha">Fecha:</label>
                                        <div class="col-sm-12">
                                                <datepicker v-model = "dato.fecha"
                                                            name = "fecha"
                                                            :format = "formatoFecha"
                                                            :language = "es"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                            v-validate="{required: true}"
                                                            :disabled-dates="fechasDeshabilitadas"
                                                            :class="{'is-invalid': errors.has('fecha')}"
                                                ></datepicker>
                                          <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                        </div>
                                    </div>
                                 </div>
                                <div class="col-md-10"></div>
                            </div>
                            <hr>
                            <div class="row">
                                <!--Concepto raíz-->
                                <div class="col-md-12" >
                                    <div class="form-group error-content">
                                    <label for="id_concepto">Concepto:</label>
                                        <concepto-select
                                                name="id_concepto"
                                                data-vv-as="Concepto"
                                                v-validate="{required: true}"
                                                id="id_concepto"
                                                v-model="id_concepto"
                                                :error="errors.has('id_concepto')"
                                                ref="conceptoSelect"
                                                :disableBranchNodes="true"
                                        ></concepto-select>
                                    <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="row">-->
                                <!--<div class="col-md-12" v-if="id_almacen && ((dato.opciones == 1 && dato.id_concepto != '') || dato.opciones == 65537)">-->
                                    <!--<div class="form-group">-->
                                        <!--<div v-if="id_almacen">-->
                                             <!--<div >-->
                                                <!--<table class="table table-striped">-->
                                                    <!--<thead>-->
                                                        <!--<tr>-->
                                                            <!--<th class="index_corto">#</th>-->
                                                            <!--<th class="no_parte_input">No. de Parte</th>-->
                                                            <!--<th>Material</th>-->
                                                            <!--<th class="unidad">Unidad</th>-->
                                                            <!--<th class="money_input">Existencia</th>-->
                                                            <!--<th class="money_input">Cantidad</th>-->
                                                            <!--<th class="icono"></th>-->
                                                            <!--<th style="width: 200px; max-width: 200px; min-width: 200px">Destino</th>-->
                                                            <!--<th style="width: 60px; max-width: 60px; min-width: 60px"></th>-->
                                                             <!--<th class="icono">-->
                                                            <!--<button type="button" class="btn btn-sm btn-outline-success" @click="agregar_partida" :disabled="cargando">-->
                                                                <!--<i class="fa fa-spin fa-spinner" v-if="cargando"></i>-->
                                                                <!--<i class="fa fa-plus" v-else></i>-->
                                                            <!--</button>-->
                                                        <!--</th>-->
                                                        <!--</tr>-->
                                                    <!--</thead>-->
                                                    <!--<tbody>-->
                                                        <!--<tr v-for="(partida, i) in partidas">-->
                                                            <!--<td>{{ i + 1}}</td>-->
                                                            <!--<td>-->
                                                                <!--<select-->

                                                                        <!--:disabled = "!bandera"-->
                                                                        <!--class="form-control"-->
                                                                        <!--:name="`id_material[${i}]`"-->
                                                                        <!--v-model="partida.material"-->
                                                                        <!--v-validate="{required: true }"-->
                                                                        <!--data-vv-as="No de Parte"-->
                                                                        <!--:class="{'is-invalid': errors.has(`id_material[${i}]`)}"-->
                                                                <!--&gt;-->
                                                                     <!--<option v-for="numero in materiales" :value="numero">{{ numero.numero_parte }}</option>-->
                                                                <!--</select>-->
                                                            <!--<div class="invalid-feedback"-->
                                                                 <!--v-show="errors.has(`id_material[${i}]`)">{{ errors.first(`id_material[${i}]`) }}-->
                                                            <!--</div>-->
                                                            <!--</td>-->
                                                            <!--<td>-->
                                                                <!--<select-->

                                                                        <!--:disabled = "!bandera"-->
                                                                        <!--class="form-control"-->
                                                                        <!--:name="`id_material[${i}]`"-->
                                                                        <!--v-model="partida.material"-->
                                                                        <!--v-validate="{required: true }"-->
                                                                        <!--data-vv-as="Descripción"-->
                                                                        <!--:class="{'is-invalid': errors.has(`id_material[${i}]`)}"-->
                                                                <!--&gt;-->
                                                                 <!--<option v-for="material in materiales" :value="material">{{ material.descripcion }}</option>-->
                                                            <!--</select>-->
                                                            <!--<div class="invalid-feedback"-->
                                                                 <!--v-show="errors.has(`id_material[${i}]`)">{{ errors.first(`id_material[${i}]`) }}-->
                                                            <!--</div>-->
                                                            <!--</td>-->
                                                            <!--<td>-->
                                                                <!--{{partida.material.unidad}}-->
                                                            <!--</td>-->
                                                            <!--<td class="money">-->
                                                                <!--{{partida.material.saldo_almacen_format}}-->
                                                            <!--</td>-->
                                                            <!--<td>-->
                                                                <!--<input-->
                                                                        <!--:disabled = "!partida.material"-->
                                                                        <!--type="number"-->
                                                                        <!--step="any"-->
                                                                        <!--:name="`cantidad[${i}]`"-->
                                                                        <!--v-model="partida.cantidad"-->
                                                                        <!--data-vv-as="Cantidad"-->
                                                                        <!--v-validate="{required: true,min_value: 0.01, max_value:partida.material.saldo_almacen, decimal:2}"-->
                                                                        <!--class="form-control"-->
                                                                        <!--:class="{'is-invalid': errors.has(`cantidad[${i}]`)}"-->
                                                                        <!--id="cantidad"-->
                                                                        <!--placeholder="Cantidad">-->
                                                            <!--<div class="invalid-feedback"-->
                                                                 <!--v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}-->
                                                            <!--</div>-->
                                                            <!--</td>-->
                                                            <!--<td  v-if="partida.destino ===  ''" >-->
                                                            <!--<small class="badge badge-secondary">-->
                                                                <!--<i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>-->
                                                            <!--</small>-->
                                                        <!--</td>-->
                                                        <!--<td v-else >-->
                                                            <!--<small class="badge badge-success" v-if="partida.destino.tipo_destino === 1" >-->
                                                                <!--<i class="fa fa-stream button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>-->
                                                            <!--</small>-->
                                                             <!--<small class="badge badge-success" v-else="partida.destino.tipo_destino === 2" >-->
                                                                <!--<i class="fa fa-boxes button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>-->
                                                            <!--</small>-->
                                                        <!--</td>-->
                                                        <!--<td  v-if="partida.destino ===  ''" >-->
                                                        <!--</td>-->
                                                        <!--<td v-else >-->
                                                            <!--<span v-if="partida.destino.tipo_destino === 1" style="text-decoration: underline"  :title="partida.destino.destino.path">{{partida.destino.destino.descripcion}}</span>-->
                                                            <!--<span v-if="partida.destino.tipo_destino === 2">{{partida.destino.destino.descripcion}}</span>-->
                                                        <!--</td>-->
                                                            <!--<td>-->
                                                                <!--<i class="far fa-copy button" v-on:click="copiar_destino(partida)" ></i>-->
                                                                <!--<i class="fas fa-paste button" v-on:click="pegar_destino(partida)" ></i>-->
                                                            <!--</td>-->
                                                            <!--<td class="icono">-->
                                                                <!--<button type="button" class="btn btn-outline-danger btn-sm" @click="borrarPartida(i)"><i class="fa fa-trash"></i></button>-->
                                                            <!--</td>-->
                                                        <!--</tr>-->
                                                    <!--</tbody>-->
                                                <!--</table>-->
                                             <!--</div>-->
                                        <!--</div>-->
                                    <!--</div>-->
                                <!--</div>-->
                                <!--<hr>-->
                                <!--<div class="col-md-12">-->
                                    <!--<div class="form-group error-content">-->
                                        <!--<label for="observaciones">Observaciones:</label>-->
                                        <!--<textarea-->
                                                <!--name="observaciones"-->
                                                <!--id="observaciones"-->
                                                <!--class="form-control"-->
                                                <!--v-model="dato.observaciones"-->
                                                <!--v-validate="{required: true}"-->
                                                <!--data-vv-as="Observaciones"-->
                                                <!--:class="{'is-invalid': errors.has('observaciones')}"-->
                                        <!--&gt;</textarea>-->
                                        <!--<div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>-->
                                    <!--</div>-->
                                <!--</div>-->
                            <!--</div>-->
                        </div>
                         <div class="footer">
                           <button type="button" class="btn btn-secondary">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 || partidas.length == 0">Guardar</button>
                        </div>
                     </form>
                    </div>
                </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import datepicker from 'vuejs-datepicker';
    import ConceptoSelect from "../../cadeco/concepto/Select";
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "venta-create",
        components: {ConceptoSelect, datepicker},
        data() {
            return {
                es : es,
                fechasDeshabilitadas : {},
                dato:{
                    fecha : '',
                    observaciones : '',
                    partidas : []
                },
                partidas : [],
                dato_partida : {
                    cantidad : '',
                    destino : ''
                },
            }
        },
        init() {
            this.cargando = true;
        },
        mounted() {
            this.dato.fecha = new Date();
            this.fechasDeshabilitadas.from= new Date();
        },
        methods: {
            formatoFecha(date) {
                return moment(date).format('DD/MM/YYYY');
            },
        }
    }
</script>

<style scoped>

</style>