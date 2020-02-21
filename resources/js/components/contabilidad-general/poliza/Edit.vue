<template>
    <span>
        <div class="modal fade" ref="modalEditPoliza" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form role="form" @submit.prevent="validate">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-pencil"></i> EDICIÓN DE PÓLIZA</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
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
                                                     v-validate="{required: true}"
                                                     name="concepto_edit"
                                                     class="form-control"
                                                     id="concepto_edit"
                                                     v-model="poliza.concepto"
                                                     placeholder="CONCEPTO DE PÓLIZA"
                                                     :class="{'is-invalid': errors.has('concepto_edit')}"
                                             ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('concepto_edit')">{{ errors.first('concepto_edit') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label ><i class="fa fa-th-list icon"></i>Movimientos</label>
                                    </div>
                                </div>
                                <div>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="bg-gray-light">#</th>
                                                <th class="bg-gray-light">Cuenta</th>
                                                <th class="bg-gray-light">Cargo</th>
                                                <th class="bg-gray-light">Abono</th>
                                                <th class="bg-gray-light">Referencia</th>
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
                                                                v-validate="{required: true}"
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
                                                            v-validate="{required: true}"
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeModal()"><i class="fa fa-times-circle"></i> Cerrar</button>
                            <button type="submit" class="btn btn-danger" v-if="$root.can('editar_poliza', true)" :disabled="errors.count() > 0"><i class="fa fa-save"></i> Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </span>
</template>

<script>

export default {
    name: "poliza-edit",
    props: ['tipo_modal','id_empresa'],
    data(){
        return {
            edit:{
                id_empresa:'',
                id : '',
                concepto: '',
                movimientos: {},
            },

        }
    },
    methods: {
        closeModal(){
            this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', null);
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

                })
        },
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
<style scoped>
    .detalle_poliza{
        font-size: 0.8em;
    }
     .form-control{
        font-size: 1em;
    }
</style>