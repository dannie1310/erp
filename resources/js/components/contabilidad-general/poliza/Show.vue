<template>
    <span>
         <button @click="find"  type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modalShowPoliza" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> CONSULTA DE PÓLIZA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span v-if="poliza" class="detalle_poliza">
                            <div class="row">
                                <div class="col-md-1 offset-9">
                                     <div class="form-group row error-content">
                                         <label for="ejercicio" class="col-md-12 col-form-label">Fecha:</label>
                                     </div>
                                 </div>
                                <div class="col-md-2">
                                     <input
                                             type="text"
                                             disabled="disabled"
                                             name="texto"
                                             class="form-control"
                                             id="ejercicio"
                                             v-model="poliza.fecha"
                                     >
                                </div>
                            </div>
                            <div class="row" >
                                <div class="col-md-2">
                                     <div class="form-group row error-content">
                                         <label for="numero_poliza" class="col-md-12 col-form-label"># Poliza:</label>

                                     </div>
                                </div>

                                <div class="col-md-2">
                                     <div class="form-group row error-content">
                                         <label for="tipo_poliza" class="col-md-12 col-form-label">Tipo de Poliza:</label>

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
                                             id="numero_poliza"
                                             v-model="poliza.folio"
                                     >
                                </div>
                                <div class="col-md-2">
                                     <input
                                             type="text"
                                             disabled="disabled"
                                             name="texto"
                                             class="form-control"
                                             id="tipo_poliza"
                                             v-model="poliza.tipo"
                                     >
                                </div>
                                <div class="col-md-8">
                                     <textarea
                                             type="text"
                                             disabled="disabled"
                                             name="concepto_show"
                                             class="form-control"
                                             id="concepto_show"
                                             v-model="poliza.concepto"
                                     ></textarea>
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
                                            <td>{{movimiento.cuenta.cuenta}}</td>
                                            <td class="money">{{movimiento.cargo_format}}</td>
                                            <td class="money">{{movimiento.abono_format}}</td>
                                            <td>{{movimiento.referencia}}</td>
                                            <td>{{movimiento.concepto}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal()">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>

export default {
    name: "poliza-show",
    props : ['id', 'id_empresa'],
    data(){
        return {
        }
    },
    methods: {
        closeModal(){
            this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', null);
            $(this.$refs.modalShowPoliza).modal('hide');
        },
        init(){
            $(this.$refs.modalShowPoliza).modal('show');
        },
        find()
        {
            this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', null);
            return this.$store.dispatch('contabilidadGeneral/poliza/find', {
                id: this.id,
                params: {include: ['movimientos_poliza'], id_empresa : this.id_empresa}
            }).then(data => {
                this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', data);
                $(this.$refs.modalShowPoliza).modal('show');
            })
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
                if(tipo_modal !== '' && tipo_modal === 1){
                    this.init();
                }
            }
        },
    }
}
</script>
<style >
</style>
