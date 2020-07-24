<template>
    <span>
        <div class="row" v-if="!cargando">
             <form role="form" @submit.prevent="validate">
            <div class="card">
                <div class="card-header">
                    <h6>EDICIÓN DE PÓLIZA</h6>
                </div>

                    <div class="card-body">
                     <span v-if="poliza" class="detalle_poliza">
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
                                 <div class="row">
                                    <div class="col-md-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="repetir_concepto" v-on:change="repiteConceptos()" v-model="repite_concepto" >
                                            <label for="repetir_concepto" class="custom-control-label" >Replicar concepto de póliza en concepto de movimientos</label>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label ><i class="fa fa-th-list icon"></i>Movimientos</label>
                                    </div>
                                </div>
                                <div>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="bg-gray-light index_corto">#</th>
                                                <th class="bg-gray-light no_parte">Cuenta</th>
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
                                                <td class="money">{{movimiento.cargo_format}}</td>
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
                                    </table>
                                </div>
                            </span>
                    </div>

            </div>
             <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeModal()"><i class="fa fa-times-circle"></i> Cerrar</button>
                            <button type="submit" class="btn btn-danger" v-if="$root.can('editar_poliza', true)" :disabled="errors.count() > 0"><i class="fa fa-save"></i> Guardar</button>
                        </div>
             </form>
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
        closeModal(){
            this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', null);
            this.repite_concepto = false;
            $(this.$refs.modalEditPoliza).modal('hide');
        },
        init(){
            $(this.$refs.modalEditPoliza).modal('show');
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.update();
                }
            });
        },
        find()
        {
            console.log(this.id_empresa, this.id)
            this.cargando = true
            this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', null);
            return this.$store.dispatch('contabilidadGeneral/poliza/find', {
                id: this.id,
                id_empresa: this.id_empresa,
                params: {include: ['movimientos_poliza'], id_empresa : this.id_empresa}
            }).then(data => {
                this.cargando = false
                this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', data);
            })
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
        }
    },

    computed: {
        poliza(){
            return this.$store.getters['contabilidadGeneral/poliza/currentPoliza'];
        }
    },
    watch:{
        tipo_modal : {
            handler(tipo_modal) {
                if(tipo_modal !== '' && tipo_modal === 2){
                    this.init();
                }
            }
        },
    }
}
</script>
<style >

</style>
