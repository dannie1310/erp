<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Registro de Contrato Proyectado
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <!-- Seccion de datos iniciales -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-2 offset-md-10">
                                        <div class="form-group error-content">
                                            <div class="form-group">
                                                <label><b>Fecha</b></label>
                                                <datepicker v-model = "fecha"
                                                            name = "fecha"
                                                            :language = "es"
                                                            :format = "formatoFecha"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control">
                                                </datepicker>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 offset-md-8 mt-3 text-left" >
                                    <label class="text-secondary">Fechas Límite </label>
                                    <hr style="color: #0056b2; margin-top:auto;" width="95%" size="10" />
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group error-content">
                                        <label for="numero">Referencia:</label>
                                        <input type="text" class="form-control"
                                                name="referencia"
                                                data-vv-as="Referencia"
                                                v-model="referencia"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('referencia')}"
                                                id="referencia"
                                                placeholder="Referencia">
                                        <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha Cotización</b></label>
                                            <datepicker v-model = "fecha_cotizacion"
                                                        name = "fecha_cotizacion"
                                                        :language = "es"
                                                        :format = "formatoFecha"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control">
                                            </datepicker>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label><b>Fecha Contratación</b></label>
                                            <datepicker v-model = "fecha_contrato"
                                                        name = "fecha_contrato"
                                                        :language = "es"
                                                        :format = "formatoFecha"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control">
                                            </datepicker>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                     <div class="form-group error-content">
                                        <label for="id_area">Área Subcontratante</label>
                                        <select
                                                type="text"
                                                name="id_area"
                                                data-vv-as="Area"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_area"
                                                v-model="id_area"
                                                :class="{'is-invalid': errors.has('id_area')}"
                                        >
                                        <option  value selected>--- Seleccione Área Subcontratante ---</option>
                                        <option v-for="area in areas_subcontratantes" :value="area.id">{{ `${area.descripcion} ` }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_area')">{{ errors.first('id_area') }}</div>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-12  text-left" >
                                <label class="text-secondary"> </label>
                                <hr style="color: #0056b2; margin-top:auto;" width="95%" size="20" />
                            </div>
                            <!-- Seccion de partidas -->
                            <div class="row">
                                 <div  class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width:3%"></th>
                                                    <th style="width:13%">Clave</th>
                                                    <th style="width:13%">Insumo</th>
                                                    <th style="width:25%">Descripción</th>
                                                    <th style="width:13%">Unidad</th>
                                                    <th style="width:13%">Cantidad</th>
                                                    <th style="width:15%">Destinos</th>
                                                    <th style="width:5%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(partida, i) in partidas">
                                                    <td>
                                                        <button @click="agregarPartida(i)" type="button" class="btn btn-sm btn-outline-success" :disabled="cargando" title="Agregar">
                                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                            <i class="fa fa-plus" v-else></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            :name="`clave[${i}]`"
                                                            data-vv-as="Clave"
                                                            v-model="partida.clave"
                                                            v-validate="{}"
                                                            :class="{'is-invalid': errors.has(`clave[${i}]`)}"
                                                            id="clave">
                                                        <div class="invalid-feedback" v-show="errors.has(`clave[${i}]`)">{{ errors.first(`clave[${i}]`) }}</div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            :name="`insumo[${i}]`"
                                                            data-vv-as="Insumo"
                                                            v-model="partida.insumo"
                                                            v-validate="{}"
                                                            :class="{'is-invalid': errors.has(`insumo[${i}]`)}"
                                                            id="insumo">
                                                        <div class="invalid-feedback" v-show="errors.has(`insumo[${i}]`)">{{ errors.first(`insumo[${i}]`) }}</div>
                                                    </td>
                                                    <td>
                                                         <input type="text" class="form-control"
                                                            readonly="readonly"
                                                            @click="editConcepto(i)"
                                                            :name="`descripcion[${i}]`"
                                                            data-vv-as="Descripción"
                                                            :placeholder="descripcionFormat(i)"
                                                            v-validate="{required: partida.descripcion ===''}"
                                                            :class="{'is-invalid': errors.has(`descripcion[${i}]`)}"
                                                            id="descripcion">
                                                        <div class="invalid-feedback" v-show="errors.has(`descripcion[${i}]`)">{{ errors.first(`descripcion[${i}]`) }}</div>
                                                    </td>
                                                    <td>
                                                        <select
                                                            :disabled="!partida.es_hoja"
                                                            type="text"
                                                            name="unidad"
                                                            data-vv-as="Unidad"
                                                            v-validate="{required: partida.es_hoja}"
                                                            class="form-control"
                                                            id="unidad"
                                                            v-model="partida.unidad"
                                                            :class="{'is-invalid': errors.has('unidad')}">
                                                            <option value>--Unidad--</option>
                                                            <option v-for="unidad in unidades" :value="unidad.unidad">{{ unidad.descripcion }}</option>
                                                        </select>
                                                        <div class="invalid-feedback" v-show="errors.has('unidad')">{{ errors.first('unidad') }}</div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            :name="`cantidad[${i}]`"
                                                            data-vv-as="Cantidad"
                                                            v-model="partida.cantidad"
                                                            v-validate="{}"
                                                            :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                            id="cantidad">
                                                        <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                                    </td>
                                                    <td>Select DESTINOS</td>
                                                    <td>
                                                        <button @click="agregarPartida(i)" type="button" class="btn btn-sm btn-outline-danger" :disabled="!partida.es_hoja && partida.cantidad_hijos > 0" title="Eliminar">
                                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                            <i class="fa fa-trash" v-else></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                         </div>    
                    </form>
                </div>
            </div>
        </div>
        <div ref="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-list" style="padding-right:3px"></i>Agregar Descripción</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="edit_concepto_index >=0">
                        <div class="col-md-12">
                            <div class="form-group error-content">
                                <input type="text" class="form-control"
                                    name="descripcion"
                                    data-vv-as="Descripción"
                                    v-model="descrip_temporal"
                                    v-validate="{required: edit_concepto_index !== ''}"
                                    :class="{'is-invalid': errors.has('descripcion')}"
                                    id="descripcion">
                                <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                
                            </div>
                       </div>
                       <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" @click="cambiarDesc()" class="btn btn-primary">Actualizar</button>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "contrato-proyectado-create",
        components: {Datepicker},
        data() {
            return {
                es: es,
                cargando: false,
                fecha: '',
                fecha_cotizacion: '',
                fecha_contrato: '',
                referencia: '',
                areas_subcontratantes:[],
                id_area:'',
                partidas:[],
                unidades:[],
                edit_concepto_index:'',
                descrip_temporal:'',
            }
        },
        mounted(){
            this.getAreaSub();
            this.getUnidades();
            this.partidas.push({
                clave:'',
                insumo:'',
                descripcion:'',
                unidad:'',
                cantidad:'',
                destino:'',
                nivel:1,
                indice:1,
                es_hoja:true,
                es_rama:false,
                cantidad_hijos:0,
            });
        },
        methods: {
            agregarPartida(index){
                if(index === 0){
                    this.partidas.push({
                        clave:'',
                        insumo:'',
                        descripcion:'',
                        unidad:'',
                        cantidad:'',
                        destino:'',
                        nivel:this.partidas[index].nivel + 1,
                        indice:1,
                        es_hoja:true,
                        es_rama:false,
                        cantidad_hijos:0,
                    });
                }else{
                    let temp_index = index + 1;
                    while(temp_index in this.partidas && this.partidas[temp_index].nivel >= +this.partidas[index].nivel + 1){
                        temp_index= temp_index + 1;
                    }
                    this.partidas.splice(temp_index, 0, {
                        clave:'',
                        insumo:'',
                        descripcion:'',
                        unidad:'',
                        cantidad:'',
                        destino:'',
                        nivel:this.partidas[index].nivel + 1,
                        indice:1,
                        es_hoja:true,
                        es_rama:false,
                        cantidad_hijos:0,
                    });
                }
                this.partidas[index].es_hoja = false;
                this.partidas[index].es_rama = true;
                this.partidas[index].cantidad_hijos = this.partidas[index].cantidad_hijos + 1;
                
            },
            cambiarDesc(){
                this.partidas[this.edit_concepto_index].descripcion = this.descrip_temporal;
                this.edit_concepto_index='';
                this.descrip_temporal='',
                $(this.$refs.modal).modal('hide')
            },
            descripcionFormat(i){
                var len = this.partidas[i].descripcion.length + (+this.partidas[i].nivel * 3);
                return this.partidas[i].descripcion.padStart(len, "_")
            },
            editConcepto(index){
                this.edit_concepto_index = index;
                this.descrip_temporal = this.partidas[index].descripcion;
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show')

            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getAreaSub() {
                this.areas_disponibles = [];
                return this.$store.dispatch('configuracion/area-subcontratante/index')
                    .then(data => {
                        this.areas_subcontratantes = data.sort((a, b) => (a.descripcion > b.descripcion) ? 1 : -1);
                    });
            },
            getUnidades() {
                return this.$store.dispatch('cadeco/unidad/index', {
                    params: {sort: 'unidad',  order: 'asc'}
                })
                    .then(data => {
                        this.unidades= data.data;
                    })
            },
        },
    }
</script>