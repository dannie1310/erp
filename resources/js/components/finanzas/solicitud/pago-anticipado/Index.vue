<template>
    <div class="row">
        <div class="col-12">
            <create @created="paginate()"></create>
        </div>
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" />
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
</template>

<script>
    import Create from "./Create";
    export default {
        name: "solicitud-pago-anticipado-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Número de Folio', field: 'numero_folio', thComp: require('../../../globals/th-Filter').default, sortable: true },
                    { title: 'Transacción Antecedente', field: 'id_antecedente', sortable: true },
                    { title: 'Monto', field: 'monto', sortable: true },
                    { title: 'Empresa', field: 'id_empresa', thComp: require('../../../globals/th-Filter').default, sortable: true },
                    { title: 'Fecha y Hora de Registro', field: 'FechaHoraRegistro', sortable: true },
                    { title: 'Observaciones', field: 'observaciones', thComp: require('../../../globals/th-Filter').default, sortable: true },
                    { title: 'Estatus', field: 'estado', tdComp: require('./partials/SolicitudEstatus').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: ['orden_compra', 'subcontrato','empresa'], sort: 'id_transaccion', order: 'desc'},
                estado: "",
                cargando: false
            }
        },

        mounted() {
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },

        methods: {
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('finanzas/solicitud-pago-anticipado/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('finanzas/solicitud-pago-anticipado/SET_SOLICITUDES', data.data);
                        this.$store.commit('finanzas/solicitud-pago-anticipado/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },
        computed: {
            solicitudes(){
                return this.$store.getters['finanzas/solicitud-pago-anticipado/solicitudes'];
            },
            meta(){
                return this.$store.getters['finanzas/solicitud-pago-anticipado/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            solicitudes: {
                handler(solicitudes) {
                    let self = this
                    self.$data.data = []
                    solicitudes.forEach(function (solicitud, i) {

                        if(solicitud.subcontrato){
                            self.$data.id_antecedente = '('+solicitud.subcontrato.tipo_nombre+') '+solicitud.subcontrato.numero_folio_format;
                            if(solicitud.subcontrato.referencia!=""){
                                self.$data.id_antecedente = self.$data.id_antecedente+' ('+solicitud.subcontrato.dato_transaccion+')';
                            }else{
                                self.$data.id_antecedente = self.$data.id_antecedente+' ---';
                            }
                        }else if(solicitud.orden_compra){
                            self.$data.id_antecedente = '('+solicitud.orden_compra.tipo_nombre+') '+solicitud.orden_compra.numero_folio_format;
                            if(solicitud.orden_compra.referencia!=""){
                                self.$data.id_antecedente = self.$data.id_antecedente+' ('+solicitud.orden_compra.dato_transaccion+')';
                            }else{
                                self.$data.id_antecedente = self.$data.id_antecedente+'---';
                            }
                        }else{
                            self.$data.id_antecedente = '';
                        }

                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            numero_folio: '# ' + solicitud.numero_folio,
                            id_antecedente: self.$data.id_antecedente,
                            monto: solicitud.monto_format,
                            id_empresa: solicitud.empresa.razon_social,
                            FechaHoraRegistro: solicitud.fecha_format,
                            observaciones: solicitud.observaciones,
                            estado: solicitud.estado,
                            buttons: $.extend({}, {
                                show: true,
                                edit: self.$root.can('editar_solicitud_pago_anticipado') ? true : false,
                                cancelar: self.$root.can('cancelar_solicitud_pago_anticipado') ? true : false,
                                pdf:true,
                                id: solicitud.id,
                                estado: solicitud.estado,
                                transaccion: {id:solicitud.id, tipo:72},
                                solicitud_autorizacion : solicitud.solicitud_pago_autorizacion_activa ? 0 : 1
                            })
                        })
                    });
                },
                deep: true
            },

            meta: {
                handler(meta) {
                    let total = meta.pagination.total
                    this.$data.total = total
                },
                deep: true
            },
            query: {
                handler(query) {
                    this.paginate(query)
                },
                deep: true
            },
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
            }
        }
    }
</script>
