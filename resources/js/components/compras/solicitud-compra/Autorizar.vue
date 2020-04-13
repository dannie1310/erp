<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-success" title="Aprobar" v-show="aprobar">
            <i class="fa fa-thumbs-o-up" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-thumbs-o-up"></i> APROBAR SOLICITUD DE COMPRA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Folio:</b></td>
                                                    <td class="bg-gray-light">{{res.numero_folio_format}}</td>
                                                    <td class="bg-gray-light"><b>Folio Compuesto:</b></td>
                                                    <td class="bg-gray-light">{{res.compuesto ? res.compuesto.folio : '----'}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Fecha Requerido:</b></td>
                                                    <td class="bg-gray-light">{{res.fecha_format}}</td>
                                                    <td class="bg-gray-light"><b>Usuario Registró:</b></td>
                                                    <td class="bg-gray-light">{{usuario}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Fecha / Hora Registro</b></td>
                                                    <td class="bg-gray-light">{{res.fecha_registro}}</td>
                                                    <td class="bg-gray-light"><b>Observaciones:</b></td>
                                                    <td class="bg-gray-light" colspan="3">{{res.observaciones}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="row">
                                            <div class="col-12">
                                                <h6><b>Detalle de las partidas</b></h6>
                                            </div>
                                        </div>
                                <div class="row">
                                    <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Material</th>
                                                        <th>Unidad</th>
                                                        <th class="no_parte">Solicitado</th>
                                                        <th class="no_parte">En O/C(s)</th>
                                                        <th class="no_parte">Surtido</th>
                                                        <th class="no_parte">Existencia</th>
                                                        <th class="money">Autorizado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(partida, i) in partidas">
                                                        <td>{{i+1}}</td>
                                                        <td >{{partida.material.descripcion}}</td>
                                                        <td style="text-align: center">{{partida.unidad}}</td>
                                                        <td style="text-align: center">{{partida.solicitado_cantidad}}</td>
                                                        <td style="text-align: center">{{partida.orden_compra_cantidad}}</td>
                                                        <td style="text-align: center">{{partida.surtido_cantidad}}</td>
                                                        <td style="text-align: center">{{partida.existencia_cantidad}}</td>
                                                        <td style="text-align: right">
                                                            <input
                                                                :disabled="cargando"
                                                                type="number"
                                                                step=".1"
                                                                :name="`autorizado[${i}]`"
                                                                data-vv-as="Autorizado"
                                                                v-validate="{required: true, min_value:0.01, max_value:partida.cantidad}"
                                                                class="form-control"
                                                                v-model="cantidad[i]"
                                                                id="autorizado"
                                                                :class="{'is-invalid': errors.has(`autorizado[${i}]`)}">
                                                    <div class="invalid-feedback" v-show="errors.has(`autorizado[${i}]`)">{{ errors.first(`autorizado[${i}]`) }}</div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                  </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary":disabled="errors.count() > 0 || partidas.length == 0">Aprobar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "aprobar-solicitud-editar",
    props: ['id', 'aprobar'],
    data() {
        return {
            cargando: false,
            res: [],
            id_solicitud: '',
            usuario:'',
            t: '',
            partidas:[],
            cantidad:[],
        }
    },
    methods: {
        save() {
                return this.$store.dispatch('compras/solicitud-compra/aprobar', {
                id: this.id,
                data: this.$data,
            })
                .then(() => {
                   return this.$store.dispatch('compras/solicitud-compra/paginate', { params: {sort: 'numero_folio', order: 'DESC'}})
                    .then(data => {
                        this.$store.commit('compras/solicitud-compra/SET_SOLICITUDES', data.data);
                        this.$store.commit('compras/solicitud-compra/SET_META', data.meta);
                    })
                   }).finally( ()=>{
                       $(this.$refs.modal).modal('hide');
                   });

        },
        find() {
            this.t = 0;
            this.cantidad = [];
            this.cargando = true;

                this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', null);
                return this.$store.dispatch('compras/solicitud-compra/find', {
                    id: this.id,
                    params: {include: ['partidas.material', 'complemento']}
                }).then(data => {

                    this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', data);
                    this.res = data;
                    this.usuario = this.res.usuario.nombre;
                    this.partidas = this.res.partidas.data;
                    while(this.t < this.partidas.length)
                    {
                        this.cantidad[this.t] = this.partidas[this.t].cantidad;
                        this.t ++;
                    }

                    $(this.$refs.modal).modal('show')
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
    },
}
</script>
<style>
    .icons
    {
        text-align: center;
    }
</style>
