<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group error-content">
                                            <label for="fecha" class="col-form-label">Fecha:</label>
                                            <datepicker v-model = "fecha"
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
                                <div class="row justify-content-between">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="id_solicitud">Buscar Solicitud:</label>
                                                 <model-list-select
                                                                name="id_solicitud"
                                                                option-value="id"                                                               
                                                                v-model="id_solicitud"
                                                                :custom-text="idFolioObservaciones"
                                                                :list="solicitudes"
                                                                :placeholder="!cargando?'Seleccionar o buscar material por descripcion':'Cargando...'">
                                                            </model-list-select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_solicitud')">{{ errors.first('id_solicitud') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-between">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_proveedor">Proovedores</label>
                                            <select class="form-control"
                                                    name="id_proveedor"
                                                    data-vv-as="Proveedores"
                                                    v-model="id_proveedor"
                                                    v-validate="{required: true}"
                                                    :error="errors.has('id_proveedor')"
                                                    id="id_proveedor">
                                                <option value>-- Seleccionar--</option>
                                                <option v-for="area in proveedores" :value="area.id" >{{ area.razon_social}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_proveedor')">{{ errors.first('id_proveedor') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="row">   
                                    <div class="col-md-12">
                                        <label for="concepto" class="col-form-label">Concepto: </label>
                                    </div>
                                </div> -->
                                <!-- <div class="row">   
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <textarea
                                                name="concepto"
                                                id="concepto"
                                                class="form-control"
                                                v-model="concepto"
                                                v-validate="{required: true}"
                                                data-vv-as="Concepto"
                                                :class="{'is-invalid': errors.has('concepto')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                                        </div>
                                    </div>
                                </div> -->
                                <hr />
                                
                                <div class="row" v-if="id_solicitud != ''">
                                    <div  class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="index_corto">#</th>
                                                    <th style="width:130px;">No. de Parte</th>
                                                    <th>Descripción</th>
                                                    <th class="unidad">Unidad</th>
                                                    <th class="cantidad_input">Cantidad Solicitada</th>
                                                    <th class="cantidad_input">Cantidad Aprobada</th>                                                    
                                                    <th style="width:140px;">Precio Unitario</th>
                                                    <th style="width:140px;">% Descuento</th>
                                                    <th class="money">Precio Total</th>
                                                    <th class="money">Moneda</th>
                                                    <th class="money">Precio Total Moneda Conversión</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                                </thead>
                                                <!-- <tbody>
                                                    <tr v-for="(partida, i) in partidas">
                                                        <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                        <td v-if="partida.i === 0 && partida.material === ''">
                                                        </td>
                                                        <td v-else-if="partida.i === 1">
                                                            <input
                                                                type="text"
                                                                data-vv-as="Número Parte"
                                                                v-validate="{required: true}"
                                                                class="form-control"
                                                                :name="`numero_parte[${i}]`"
                                                                placeholder="Número Parte"
                                                                v-model="partida.numero_parte"
                                                                :class="{'is-invalid': errors.has(`numero_parte[${i}]`)}">
                                                            <div class="invalid-feedback" v-show="errors.has(`numero_parte[${i}]`)">{{ errors.first(`numero_parte[${i}]`) }}</div>
                                                        </td>
                                                        <td v-else>{{partida.material.numero_parte}}</td>
                                                        <td v-if="partida.i === 0 && partida.material === ''">
                                                            <model-list-select
                                                                :name="`material[${i}]`"
                                                                v-validate="{required: true}"
                                                                v-model="partida.id_material"
                                                                :onchange="changeSelect(partida)"
                                                                option-value="id"
                                                                :custom-text="idAndNumeroParteAndDescripcion"
                                                                :list="materiales"
                                                                :placeholder="!cargando?'Seleccionar o buscar material por descripcion':'Cargando...'"
                                                                :isError="errors.has(`material[${i}]`)">
                                                            </model-list-select>
                                                                  <div class="invalid-feedback" v-show="errors.has('id_material')">{{ errors.first('id_material') }}</div>
                                                        </td>
                                                        <td v-else-if="partida.i === 1">
                                                            <input
                                                                type="text"
                                                                data-vv-as="Descripción"
                                                                v-validate="{required: true}"
                                                                class="form-control"
                                                                :name="`descripcion[${i}]`"
                                                                placeholder="Descripción"
                                                                v-model="partida.descripcion"
                                                                :class="{'is-invalid': errors.has(`descripcion[${i}]`)}">
                                                            <div class="invalid-feedback" v-show="errors.has(`descripcion[${i}]`)">{{ errors.first(`descripcion[${i}]`) }}</div>
                                                        </td>
                                                        <td v-else>{{partida.material.descripcion}}</td>
                                                        <td v-if="partida.i === 0">
                                                            <button  type="button" class="btn btn-outline-primary btn-sm" @click="manual(i)" title="Ingresar material manualmente"><i class="fa fa-hand-paper-o" /></button>
                                                        </td>
                                                        <td v-else-if="partida.i === 1">
                                                            <button type="button" class="btn btn-outline-primary btn-sm" @click="busqueda(i)" title="Buscar material"><i class="fa fa-refresh" /></button>
                                                        </td>
                                                        <td style="width: 30px;" v-else></td>
                                                        <td>
                                                            <input type="number"
                                                                   min="0.01"
                                                                   step=".01"
                                                                   class="form-control"
                                                                   :name="`cantidad[${i}]`"
                                                                   data-vv-as="Cantidad"
                                                                   v-validate="{required: true}"
                                                                   :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                                   v-model="partida.cantidad"/>
                                                            <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                                        </td>
                                                        <td style="width:120px;" v-if="partida.i === 1">
                                                            <select
                                                                type="text"
                                                                :name="`unidad[${i}]`"
                                                                data-vv-as="Unidad"
                                                                v-validate="{required: true}"
                                                                class="form-control"
                                                                id="unidad"
                                                                v-model="partida.unidad"
                                                                :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                                    <option value>--Unidad--</option>
                                                                    <option v-for="unidad in unidades" :value="unidad.unidad">{{ unidad.descripcion }}</option>
                                                            </select>
                                                            <div class="invalid-feedback" v-show="errors.has(`unidad[${i}]`)">{{ errors.first(`unidad[${i}]`) }}</div>
                                                        </td>
                                                        <td style="width:120px;" v-else-if="partida.unidad">{{partida.unidad}}</td>
                                                        <td style="width:120px;" v-else>{{partida.material.unidad}}</td>
                                                        <td class="fecha">
                                                            <datepicker v-model="partida.fecha"
                                                                        :name="`fecha[${i}]`"
                                                                        :format = "formatoFecha"
                                                                        :language = "es"
                                                                        :bootstrap-styling = "true"
                                                                        class = "form-control"
                                                                        v-validate="{required: true}"
                                                                        :disabled-dates="fechasDeshabilitadasHasta"
                                                                        :class="{'is-invalid': errors.has(`fecha[${i}]`)}"
                                                            ></datepicker>
                                                             <div class="invalid-feedback" v-show="errors.has(`fecha[${i}]`)">{{ errors.first(`fecha[${i}]`) }}</div>
                                                        </td>
                                                        <td style="text-align:center;"><small class="badge badge-secondary">
                                                                <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)"></i>
                                                            </small></td>
                                                        <td style="width:140px;" v-if="partida.clave_concepto"><u>{{partida.clave_concepto.descripcion}}</u></td>
                                                        <td style="width:140px; text-align:center;" v-else-if="partida.destino">{{partida.destino.descripcion}}</td>
                                                            <td style="width:140px; text-align:center;" v-else></td>
                                                        <td style="width:200px;">
                                                            <textarea class="form-control"
                                                                      :name="`observaciones[${i}]`"
                                                                      data-vv-as="Observaciones"
                                                                      v-validate="{required: true}"
                                                                      :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                      v-model="partida.observaciones"/>
                                                             <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                                        </td>
                                                        <td>
                                                            <button  type="button" class="btn btn-outline-danger btn-sm" @click="destroy(i)"><i class="fa fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                </tbody> -->
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">   <!--Obserbaciones label-->
                                    <div class="col-md-12">
                                        <label for="observaciones" class="col-form-label">Observaciones: </label>
                                    </div>
                                </div>
                                <div class="row">   <!--Observaciones text area-->
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <textarea
                                                name="observaciones"
                                                id="observaciones"
                                                class="form-control"
                                                v-model="observaciones"
                                                v-validate="{required: true}"
                                                data-vv-as="Observaciones"
                                                :class="{'is-invalid': errors.has('observaciones')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- </form> -->
                            </div>
                             <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                                    <button type="submit" :disabled="id_solicitud == ''" class="btn btn-primary">Registrar</button>
                             </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "cotizacion-create",
        components: {Datepicker, ModelListSelect},
        data() {
            return {
                cargando: false,
                id_solicitud: '',
                es:es,
                fechasDeshabilitadas:{},
                fecha : '',
                tipos : [],
                proveedores : [],
                id_proveedor : '',
                id_tipo : '',
                solicitudes : [],
                concepto : '',
                observaciones : '',                
            }
        },
        mounted() {
            this.fecha = new Date();
            this.$validator.reset();
            this.getProveedores();
            this.getSolicitudes();
            
        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[${item.numero_folio_format}] ---- [ ${item.observaciones} ]`;
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getProveedores() {
                // return this.$store.dispatch('configuracion/area-compradora/index', {
                //     params: {scope: 'asignadas', sort: 'descripcion', order: 'asc'}
                // })
                //     .then(data => {
                //         this.proveedores = data;
                //     })
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'proveedor', include: 'sucursales' }
                })
                    .then(data => {
                        this.proveedores = data.data;
                    })
            },
            getTipos() {
                console.log('tipos', this.id_solicitud);
                
                return this.$store.dispatch('configuracion/ctg-tipo/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.tipos = data.data;
                        this.disabled = false;
                    })
            },
            salir(){
                console.log(this.id_solicitud);
                
                this.$router.push({name: 'cotizacion'});
            },
            find() {

                this.cargando = true;
                this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', null);
                return this.$store.dispatch('compras/solicitud-compra/find', {
                    id: this.id_solicitud,
                    params:{include: [
                            'complemento',
                            'partidas.complemento',
                            'partidas.entrega',
                            'cotizaciones']}
                }).then(data => {
                    this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', data);

                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                    this.cargando = false;
                })
            },
            getSolicitudes() {
                this.solicitudes = [];
                this.cargando = true;
                return this.$store.dispatch('compras/solicitud-compra/index', {
                    params: {
                        scope: 'conItems',
                        limit: 100,
                        order: 'DESC',
                        sort: 'numero_folio'
                    }
                })
                    .then(data => {
                        this.solicitudes = data.data;
                        this.cargando = false;
                    })
            },
            validate() {
                
                this.$validator.validate().then(result => {
                    if (result) {
                        alert('validate');
                        // this.store()
                    }
                });
            },
            store() {
                this.t = 0;
                this.m = 0;
                 while(this.t < this.partidas.length){
                     if(typeof this.partidas[this.t].clave_concepto === 'undefined' || this.partidas[this.t].clave_concepto === '')
                        {
                            this.m ++;
                            swal('¡Error!', 'Ingrese un destino válido en partida '+(this.t + 1) +'.', 'error');
                        }
                        this.t ++;
                }if(this.m == 0)
                {
                    return this.$store.dispatch('compras/requisicion/store', this.$data)
                    .then((data) => {
                        this.$router.push({name: 'requisicion'});
                    });

                }
            },
        },
        watch: {
            id_concepto_temporal(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.destino_seleccionado.id_destino = value;
                    this.getConcepto();
                }
            },
            id_solicitud(value){
                console.log('Solicitud watch', value);
                // this.getTipos();
                this.find();
                
                
                // if(value !== '' && value !== null && value !== undefined){
                //     this.destino_seleccionado.id_destino = value;
                //     this.getConcepto();
                // }
            }

        }
    }
</script>

<style scoped>

</style>
