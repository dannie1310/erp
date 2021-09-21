<template>
    <span>
        <div class="card" v-if="!contrato">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <form role="form" @submit.prevent="validate">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group error-content">
                                <label class="col-form-label">Fecha</label>
                                <datepicker v-model="contrato.fecha_date"
                                            name = "fecha"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('fecha')}"
                                />
                                <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6><b>Fechas Límite</b></h6>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row error-content">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="cotizacion">Cotización</label>
                                        <input  v-model="contrato.cumplimiento"
                                            type="date"
                                            name="cotizacion"
                                            id="cotizacion"
                                            class="form-control"
                                            v-validate="{required: true, date_format: 'yyyy-MM-dd'}"
                                            data-vv-as="Cotización"
                                            :class="{'is-invalid': errors.has('cotizacion')}">
                                        <div class="invalid-feedback" v-show="errors.has('cotizacion')">{{ errors.first('cotizacion') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="contratacion">Contratación</label>
                                        <input  v-model="contrato.vencimiento"
                                                type="date"
                                                name="contratacion"
                                                id="contratacion"
                                                class="form-control"
                                                v-validate="{required: true, date_format: 'yyyy-MM-dd'}"
                                                data-vv-as="Contratación"
                                                :class="{'is-invalid': errors.has('contratacion')}"
                                        >
                                        <div class="invalid-feedback" v-show="errors.has('contratacion')">{{ errors.first('contratacion') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row error-content">
                                <label for="referencia" class="col-sm-2 col-form-label">Referencia</label>
                                <div class="col-sm-12">
                                    <input
                                        v-model="contrato.referencia"
                                        type="text"
                                        name="referencia"
                                        data-vv-as="Referencia"
                                        v-validate="{required: true}"
                                        class="form-control"
                                        id="referencia"
                                        placeholder="Referencia"
                                        :class="{'is-invalid': errors.has('referencia')}">
                                    <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div  class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th class="index_corto"></th>
                                        <th class="c120">Clave</th>
                                        <th >Descripción</th>
                                        <th class="c150">Unidad</th>
                                        <th class="c150">Cantidad</th>
                                        <th >Destinos</th>
                                        <th class="c100"></th>
                                        <th class="index_corto"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(partida, i) in contrato.contratos.data">
                                        <td class="icono">
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
                                                   v-validate="{max:140}"
                                                   :class="{'is-invalid': errors.has(`clave[${i}]`)}"
                                                   :id="`clave[${i}]`">
                                            <div class="invalid-feedback" v-show="errors.has(`clave[${i}]`)">{{ errors.first(`clave[${i}]`) }}</div>
                                        </td>
                                        <td>
                                             <input type="text" class="form-control"
                                                    v-model="partida.descripcion"
                                                    readonly="readonly"
                                                    @click="habilitar(i, $event)"
                                                    @focusout="deshabilitar(i, $event)"
                                                    :name="`descripcion[${i}]`"
                                                    data-vv-as="Descripción"
                                                    v-validate="{required: partida.descripcion ===''}"
                                                    :class="{'is-invalid': errors.has(`descripcion[${i}]`) || partida.error ==1 || partida.descripcion_sin_formato.length > 255}"
                                                    :id="`descripcion_${i}`">
                                            <div class="invalid-feedback" v-show="errors.has(`descripcion[${i}]`)">{{ errors.first(`descripcion[${i}]`) }}</div>
                                            <div class="error-label" v-show="partida.descripcion_sin_formato.length > 255">La longitud del campo Descripción no debe ser mayor a 255 caracteres.</div>
                                        </td>
                                        <td>
                                            <select
                                                :disabled="!partida.es_hoja"
                                                type="text"
                                                :name="`unidad[${i}]`"
                                                data-vv-as="Unidad"
                                                v-validate="{required: partida.es_hoja}"
                                                class="form-control"
                                                :id="`unidad[${i}]`"
                                                v-model="partida.unidad"
                                                :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                <option value>--Unidad--</option>
                                                <option v-for="unidad in unidades" :value="unidad.unidad">{{ unidad.descripcion }}</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has(`unidad[${i}]`)">{{ errors.first(`unidad[${i}]`) }}</div>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" :disabled="!partida.es_hoja"
                                                   :name="`cantidad[${i}]`"
                                                   data-vv-as="Cantidad"
                                                   step="any"
                                                   v-model="partida.cantidad_original"
                                                   v-validate="{required: partida.es_hoja, decimal:4}"
                                                   :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                   :id="`cantidad[${i}]`">
                                            <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                        </td>
                                        <td v-if="partida.destino">
                                            <input type="text" class="form-control"
                                                   readonly="readonly"
                                                   :title="partida.destino_path"
                                                   :name="`destino_path[${i}]`"
                                                   data-vv-as="Destino"
                                                   v-model="partida.destino"
                                                   v-validate="{required: partida.es_hoja}"
                                                   :class="{'is-invalid': errors.has(`destino_path[${i}]`)}"
                                                   :id="`destino_path[${i}]`">
                                            <div class="invalid-feedback" v-show="errors.has(`destino_path[${i}]`)">{{ errors.first(`destino_path[${i}]`) }}</div>
                                        </td>
                                        <td v-else>
                                            {{partida.destino.concepto.path_corta}}
                                        </td>
                                        <td class="icono">
                                            <small class="badge badge-secondary">
                                                <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)" v-if="partida.es_hoja"></i>
                                            </small>
                                            <i class="far fa-copy button" v-on:click="copiar_destino(partida)" v-if="partida.es_hoja"></i>
                                            <i class="fas fa-paste button" v-on:click="pegar_destino(i)" v-if="partida.es_hoja"></i>
                                        </td>
                                        <td class="icono">
                                            <button @click="eliminarPartida(i)" type="button" class="btn btn-sm btn-outline-danger pull-left" :disabled="!partida.es_hoja && partida.cantidad_hijos > 0" title="Eliminar">
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
                    <button type="button" class="btn btn-secondary" v-on:click="salir">
                        <i class="fa fa-angle-left"></i>
                        Regresar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </span>
</template>
<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
export default {
    name: "contrato-proyectado-editar",
    components: {Datepicker, es},
    props: ['id'],
    data() {
        return {
            cargando: false,
            es:es,
            contrato: [],
            fecha: '',
            vencimiento: '',
            cumplimiento: '',
            referencia: ''
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        salir() {
            this.$router.push({name: 'proyectado'});
        },
        save() {

            if(this.contrato.fecha_date == this.fecha && this.contrato.vencimiento == this.vencimiento && this.contrato.cumplimiento == this.cumplimiento && this.contrato.referencia == this.referencia)
            {
                swal('¡Error!', 'Favor de ingresar datos actualizados.', 'error')
            }else{

                return this.$store.dispatch('contratos/contrato-proyectado/update', {
                id: this.id,
                data: this.contrato,
            })
                .then(() => {
                   return this.$store.dispatch('contratos/contrato-proyectado/paginate', { params: {include: 'areasSubcontratantes', sort: 'numero_folio', order: 'DESC'}})
                    .then(data => {
                        this.$store.commit('contratos/contrato-proyectado/SET_CONTRATOS', data.data);
                        this.$store.commit('contratos/contrato-proyectado/SET_META', data.meta);
                    })
                   }).finally( ()=>{
                       $(this.$refs.modal).modal('hide');
                   });
            }
        },
        formatoFecha(date)
        {
                return moment(date).format('DD/MM/YYYY');
        },
        find()
        {
            this.cargando = true;
            return this.$store.dispatch('contratos/contrato-proyectado/find', {
                id: this.id,
                params:{ include: [ 'contratos.destino' ]}
            }).then(data => {
                this.contrato = data;
                this.fecha = data.fecha_date;
                this.referencia = data.referencia;
                this.cumplimiento = data.cumplimiento;
                this.vencimiento = data.vencimiento;
            }).finally(() => {
                this.cargando = false;
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.save()
                }
            });
        },
    }
}
</script>
<style>
    .icons
    {
        text-align: center;
    }
</style>
