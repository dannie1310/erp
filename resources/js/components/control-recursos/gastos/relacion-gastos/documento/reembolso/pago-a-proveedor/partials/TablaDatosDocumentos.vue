<template>
    <span>
        <div class="row" v-if="documentos">
        <div class="col-md-12">
           <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                    <tr>
                        <th class="index_corto encabezado">#</th>
                        <th class="c100 encabezado">Segmento Negocio</th>
                        <th class="c80 encabezado">Tipo Gasto</th>
                        <th class="c80 encabezado">Importe</th>
                        <th class="c100 encabezado">IVA</th>
                        <th class="c100 encabezado">Retenciones</th>
                        <th class="c100 encabezado">Otros Impuestos</th>
                        <th class="c100 encabezado">Total</th>
                        <th class="c30 encabezado">F</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(d, i) in documentos.data" v-on:click="abrirModal(d)">
                        <td style="text-align:center; vertical-align:inherit;" >{{i+1}}</td>
                        <td>{{ d.centro_costo }}</td>
                        <td>{{ d.tipoGastoComp.tipoGasto.descripcion }}</td>
                        <td style="text-align:right;">{{ d.importe_format }}</td>
                        <td style="text-align:right;">{{ d.iva_format }}</td>
                        <td style="text-align:right;">{{ d.retenciones_format }}</td>
                        <td style="text-align:right;">{{ d.otros_imp_format }}</td>
                        <td style="text-align:right;">{{ d.total_format }}</td>
                        <td>{{d.facturable}}</td>
                   </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-close"></i> Captura de Segmentos de Negocio</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="centros == []">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Segmentos de Negocio:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="form-group error-content">
                                                    <select class="form-control"
                                                            data-vv-as="Caja Chica"
                                                            id="id_caja"
                                                            name="id_caja"
                                                            :class="{'is-invalid': errors.has('id_caja')}"
                                                            v-validate="{required: true}"
                                                            v-model="id_caja">
                                                        <option value>-- Selecionar --</option>
                                                        <option v-for="(c) in centros" :value="c.id">{{ c.descripcion }}</option>
                                                    </select>
                                                    <div style="display:block" class="invalid-feedback" v-show="errors.has('id_caja')">{{ errors.first('id_caja') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Tipo de Gasto:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h6>{{doc.tipoGastoComp.tipoGasto.descripcion}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="salir"><i class="fa fa-close"></i> Cerrar</button>
                            <button type="submit" class="btn btn-primary" @click="desactivar" :disabled="errors.count() > 0" v-if="camion">
                                <i class="fa fa-save"></i>Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "documentos",
    props: ['documentos'],
    data() {
        return {
            cargando : true,
            id: 0,
            centros: [],
            doc: []
        }
    },
    mounted() {
        this.getCentroCosto();
    },
    methods :{
        salir() {
            $(this.$refs.modal).modal('hide');
        },
        abrirModal(doc)
        {
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
            console.log("HOLA", doc)
            this.doc = doc;
        },
        getCentroCosto() {
            return this.$store.dispatch('controlRecursos/centro-costo/index', {
                params: { order: 'asc', scope:'descripcion' }
            }).then(data => {
                this.centros = data.data;
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.editar()
                }
            });
        },
        editar() {
            return this.$store.dispatch('acarreos/camion/desactivar', {
                id: this.id,
                params: {motivo: this.motivo}
            })
                .then((data) => {
                    this.$store.commit('acarreos/camion/UPDATE_CAMION', data);
                    this.salir()
                })
        }
    }
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}
td, th{
    border: 1px #dee2e6 solid;
}

</style>
