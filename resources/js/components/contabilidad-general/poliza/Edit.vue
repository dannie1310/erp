<template>
    <span>
        <div class="row" v-if="!cargando">
            <div class="col-12">
            <form role="form" @submit.prevent="validate" v-if="poliza" class="detalle_poliza">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-1 offset-9">
                                <div class="form-group row error-content">
                                    <label for="fecha" class="col-md-12 col-form-label">Fecha:</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <input
                                    type="text"
                                    disabled="disabled"
                                    name="texto"
                                    class="form-control"
                                    id="fecha"
                                    v-model="poliza.fecha"
                                >
                            </div>
                        </div>
                        <div class="row" >
                            <div class="col-md-2">
                                <div class="form-group row error-content">
                                    <label for="numero_poliza_edit" class="col-md-12 col-form-label"># Poliza:</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group row error-content">
                                    <label for="tipo_poliza_edit" class="col-md-12 col-form-label">Tipo de Poliza:</label>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label for="texto" class="col-md-12 col-form-label">Concepto:</label>
                            </div>
                        </div>
                        <div class="row" >
                            <div class="col-md-2">
                                <input
                                    type="text"
                                    disabled="disabled"
                                    name="texto"
                                    class="form-control"
                                    id="numero_poliza_edit"
                                    v-model="poliza.folio"
                                >
                            </div>
                            <div class="col-md-2">
                                <input
                                    type="text"
                                    disabled="disabled"
                                    name="texto"
                                    class="form-control"
                                    id="tipo_poliza_edit"
                                    v-model="poliza.tipo"
                                >
                            </div>
                            <div class="col-md-8">
                                <div class="form-group row error-content">
                                    <textarea
                                        type="text"
                                        v-validate="{required: true, max:100}"
                                        name="concepto_edit"
                                        class="form-control"
                                        id="concepto_edit"
                                        v-model="poliza.concepto"
                                        placeholder="CONCEPTO DE PÓLIZA"
                                        :class="{'is-invalid': errors.has('concepto_edit')}"
                                        v-on:keyup ="repiteConceptos()"
                                    ></textarea>
                                    <div class="invalid-feedback" v-show="errors.has('concepto_edit')">{{ errors.first('concepto_edit') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                         <div class="row">
                             <div class="col-md-12">
                                 <div class="custom-control custom-checkbox">
                                     <input type="checkbox" class="custom-control-input" id="repetir_concepto" v-on:change="repiteConceptos()" v-model="repite_concepto" >
                                     <label for="repetir_concepto" class="custom-control-label" >Replicar concepto de póliza en concepto de movimientos</label>
                                 </div>
                             </div>
                         </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <label ><i class="fa fa-th-list icon"></i>Movimientos</label>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="bg-gray-light index_corto">#</th>
                                <th class="bg-gray-light no_parte">Cuenta</th>
                                <th class="bg-gray-light">Tipo Cuenta</th>
                                <th class="bg-gray-light">Cargo</th>
                                <th class="bg-gray-light">Abono</th>
                                <th class="bg-gray-light referencia_input">Referencia</th>
                                <th class="bg-gray-light">Concepto</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(movimiento, i) in poliza.movimientos_poliza.data">
                                <td>{{ i + 1 }}</td>
                                <td>{{movimiento.cuenta}}</td>
                                <td>
                                    <select
                                        class="form-control"
                                        :name="`id_tipo_movimiento_poliza[${i}]`"
                                        v-model="movimiento.tipo"
                                        v-validate="{required: true}"
                                        data-vv-as="Tipo"
                                        :class="{'is-invalid': errors.has(`id_tipo_movimiento_poliza[${i}]`)}"
                                        >
                                        <option value="0">Cargo</option>
                                        <option value="1">Abono</option>
                                    </select>
                                    <div class="invalid-feedback"
                                         v-show="errors.has(`id_tipo_movimiento_poliza[${i}]`)">{{ errors.first(`id_tipo_movimiento_poliza[${i}]`) }}
                                    </div>
                                </td>
                                <td class="money" v-if="movimiento.tipo == 0">
                                    <input
                                        type="number"
                                        step="any"
                                        class="form-control"
                                        :name="`importe[${i}]`"
                                        v-model="movimiento.importe"
                                        v-validate="{required: true, decimal: true}"
                                        data-vv-as="Debe"
                                        :class="{'is-invalid': errors.has(`importe[${i}]`)}"
                                    />
                                    <div class="invalid-feedback" v-show="errors.has(`importe[${i}]`)">{{ errors.first(`importe[${i}]`) }}</div>
                                </td>
                                <td v-else></td>
                                <!-- <td v-else> ${{ parseFloat(movimiento.importe).formatMoney(2, '.', ',') }}</td>-->
                                <td class="money">{{movimiento.abono_format}}</td>
                                <td>
                                    <div class="form-group row error-content">
                                        <input
                                            type="text"
                                            v-validate="{required: true, max:30}"
                                            :name="`referencia[${i}]`"
                                            class="form-control"
                                            :id="`referencia[${i}]`"
                                            v-model="movimiento.referencia"
                                            placeholder="REFERENCIA DE MOVIMIENTO"
                                            :class="{'is-invalid': errors.has(`referencia[${i}]`)}"
                                        />
                                        <div class="invalid-feedback" v-show="errors.has(`referencia[${i}]`)">{{ errors.first(`referencia[${i}]`) }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group row error-content">
                                        <textarea
                                            type="text"
                                            v-validate="{required: true, max:100}"
                                            :name="`concepto_movto_edit[${i}]`"
                                            class="form-control"
                                            :id="`concepto_movto_edit[${i}]`"
                                            v-model="movimiento.concepto"
                                            placeholder="CONCEPTO DE MOVIMIENTO"
                                            :class="{'is-invalid': errors.has(`concepto_movto_edit[${i}]`)}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has(`concepto_movto_edit[${i}]`)">{{ errors.first(`concepto_movto_edit[${i}]`) }}</div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="2" class="text-center" :class="color">
                                    <b>Sumas Iguales</b>
                                </th>
                                <th :class="color">
                                    <b>$&nbsp;{{(parseFloat(sumaDebe)).formatMoney(2,'.',',')}}</b>
                                </th>
                                <th :class="color">
                                    <b>$&nbsp;{{(parseFloat(sumaHaber)).formatMoney(2,'.',',')}}</b>
                                </th>
                                <th :class="color" colspan="3"></th>
                            </tr>
                            </tfoot>
                        </table>
                        <div class="col-sm-12" style="text-align: right">
                            <h4><b>Total de la Póliza:</b>
                                $&nbsp;{{ (parseFloat(poliza.total)).formatMoney(2, '.', ',') }}
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal()"><i class="fa fa-times-circle"></i> Cerrar</button>
                    <button type="submit" class="btn btn-danger" v-if="$root.can('editar_poliza', true)" :disabled="errors.count() > 0"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
            </div>
        </div>
    </span>
</template>

<script>

export default {
    name: "poliza-edit",
    props: ['id','id_empresa'],
    data(){
        return {
            repite_concepto : false,
            cargando : false,
            original: null,
            edit:{
                id_empresa:'',
                id : '',
                concepto: '',
                movimientos: {},
            },

        }
    },
    mounted() {
      this.find();
    },
    methods: {
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.update();
                }
            });
        },
        find()
        {
            if(this.id_empresa === undefined) {
                swal("Error iniciar nuevamente el proceso", {
                    icon: "error",
                    //timer: 2500,
                    buttons: {
                        confirm: {
                            text: 'OK',
                            closeModal: true,
                        }
                    }
                }).then(() => {
                    this.salir()
                })
            }else {
                this.cargando = true
                this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', null);
                return this.$store.dispatch('contabilidadGeneral/poliza/find', {
                    id: this.id,
                    id_empresa: this.id_empresa,
                    params: {include: ['movimientos_poliza'], id_empresa: this.id_empresa}
                }).then(data => {
                    this.cargando = false
                    this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', data);
                })
            }
        },
        fillEdit() {

            this.edit.id = this.poliza.id;
            this.edit.concepto = this.poliza.concepto;
            this.edit.id_empresa = this.id_empresa;
            let self = this;
            (self.poliza.movimientos_poliza.data).forEach(function (movimiento, i) {
                self.edit.movimientos[i] = {id:movimiento.id, concepto : movimiento.concepto, referencia : movimiento.referencia};
            });
        },
        update(){
            this.fillEdit();
            return this.$store.dispatch('contabilidadGeneral/poliza/update', {
                id: this.poliza.id,
                data: this.edit,
            })
                .then(data => {
                    this.$store.commit('contabilidadGeneral/poliza/UPDATE_POLIZA', data);
                }).finally(()=>{
                    this.closeModal();
                })
        },
        repiteConceptos(){
            if(this.repite_concepto === true ){
                let self = this;
                this.poliza.movimientos_poliza.data.forEach(function(movimiento, i){
                    movimiento.concepto = self.poliza.concepto;
                });
            }
        },
        salir(){
            this.$router.push({ name:'poliza-contpaq', params: {}});
        }
    },

    computed: {
        poliza(){
            return this.$store.getters['contabilidadGeneral/poliza/currentPoliza'];
        },
       /* currentPoliza() {
            return this.$store.getters['contabilidadGeneral/poliza/currentPoliza']
        },*/
        diff() {
            return diff(this.poliza, this.original)
        },
        sumaDebe() {
            let result = 0;
            this.poliza.movimientos_poliza.data.forEach(function (movimiento, i) {
                if (movimiento.tipo == 1) {
                    result += parseFloat(movimiento.importe);
                }
            })
            return result
        },
        sumaHaber() {
            let result = 0;
            this.poliza.movimientos_poliza.data.forEach(function (movimiento, i) {
                if (movimiento.tipo == 2) {
                    result += parseFloat(movimiento.importe);
                }
            })
            return result
        },
        cuadrado() {
            return Math.abs(this.sumaDebe - this.sumaHaber) <= 0.99;
        },
        color() {
            if (!this.cuadrado) {
                return 'bg-danger'
            } else {
                return 'bg-gray'
            }
        },
    },
    watch:{
        poliza: {
            handler(poliza) {
                if (poliza) {
                    this.poliza = JSON.parse(JSON.stringify(poliza));
                    this.original = JSON.parse(JSON.stringify(poliza));
                }
            },
            deep: true
        }
    }
}
</script>
<style >

</style>
