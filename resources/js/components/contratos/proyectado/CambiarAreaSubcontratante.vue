<template>
    <span>
        <button @click="init" type="button" class="btn btn-sm btn-outline-success" :disabled="cargando" title="Actualizar área">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-refresh" v-else></i>
        </button>
        <div ref="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-search" style="padding-right:3px"></i>Cambiar Area Subcontratante</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <form role="form" @submit.prevent="validate">
                    <div class="modal-body">
                       <div class="col-md-6">
                           <h5>Número de folio: {{value.numero_folio}}</h5>
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
                                <option v-if="value.area != null" value>{{value.area.descripcion}}</option>
<!--                                <option selected>&#45;&#45;Área Subcontratante sin Asignar&#45;&#45;</option>-->
                                <option v-else value>--- Área Subcontratante sin Asignar ---</option>
                                <option v-for="area in areas_disponibles" :value="area.id">{{ `${area.descripcion} ` }}</option>
                                </select>
                                <div class="invalid-feedback" v-show="errors.has('id_area')">{{ errors.first('id_area') }}</div>
                            </div>
                            </div>
                       </div>
                       <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                       </div>
                </form>
                    </div>
                </div>
            </div>
    </span>
</template>

<script>
    export default {
        name: "cambiar-area-subcontratante",
        components: {},
        props: ['id','value'],
        data() {
            return {
                id_area: '',
                contrato_proyectado : null,
                cargando: false,
                areas_disponibles: [],
                areas_asignadas: [],
            }
        },
        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            }
        },
        methods: {
            init(){
                this.find({id: this.id}).then(data=>{

                    this.contrato_proyectado = data
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
            find(payload) {
                this.cargando = true;
                return this.$store.dispatch('contratos/contrato-proyectado/find', {
                    id: payload.id,
                }).finally(() => {
                        this.getAreaSub();
                    })

            },
            getAreaSub() {
                this.areas_disponibles = [];
                return this.$store.dispatch('configuracion/area-subcontratante/index')
                    .then(data => {
                        this.areas_disponibles = data.sort((a, b) => (a.descripcion > b.descripcion) ? 1 : -1);
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.actualiza()
                    }
                });
            },
            actualiza() {
                return this.$store.dispatch('contratos/contrato-proyectado/actualiza', {
                    id: this.id,
                    data: {
                        id_area: this.id_area
                    },
                    params:{include:"areasSubcontratantes"}
                })
                    .then(data => {
                      return this.$store.dispatch('contratos/contrato-proyectado/paginate', { params: {include: 'areasSubcontratantes', sort: 'numero_folio', order: 'DESC'}})
                          .then(data => {
                            this.$store.commit('contratos/contrato-proyectado/SET_CONTRATOS', data.data);
                            this.$store.commit('contratos/contrato-proyectado/SET_META', data.meta);
                          })
                        /*$(this.$refs.modal).modal('hide');
                        this.$store.dispatch('configuracion/area-subcontratante/getAreasUsuario', this.currentUser.idusuario)
                            .then(data_a => {
                                var areas = [];
                                data_a.data.forEach(area => {
                                    areas.push(area.id);
                                });

                                if($.inArray(data.areasSubcontratantes.data[0].id, areas) == -1) {
                                    this.$store.commit('contratos/contrato-proyectado/DELETE_CONTRATO', this.id);
                                }

                            })*/
                    })
                    .finally( ()=>{
                      $(this.$refs.modal).modal('hide');
                    });
            },

        }

    }
</script>

<style>

</style>
