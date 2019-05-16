<template>
    <div class="row">
        <div class="col-12">
            <create></create>
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
                    { title: '# Folio', field: 'numero_folio', sortable: true },
                    { title: 'Rubro', field: 'rubro', sortable: false },
                    { title: 'Transacción Antecedente', field: 'antecedente', sortable: false },
                    { title: 'Monto', field: 'monto', sortable: false },
                    { title: 'Beneficiario', field: 'beneficiario', sortable: false },
                    { title: 'Fecha y Hora de Registro', field: 'fecha_registro', sortable: false },
                    { title: 'Observaciones', field: 'observaciones', sortable: false },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {include: ['transaccion_rubro', 'orden_compra', 'subcontrato','empresa']},
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
                        if(solicitud.transaccion_rubro){
                            self.$data.rubro = solicitud.transaccion_rubro.rubro.descripcion;
                        }else{
                            self.$data.rubro = '';
                        }

                        if(solicitud.subcontrato){
                            self.$data.antecedente = '('+solicitud.subcontrato.tipo_nombre+') '+solicitud.subcontrato.numero_folio_format;
                            if(solicitud.subcontrato.referencia!=""){
                                self.$data.antecedente = self.$data.antecedente+' ('+solicitud.subcontrato.referencia+')';
                            }else{
                                self.$data.antecedente = self.$data.antecedente+' ---';
                            }
                        }else if(solicitud.orden_compra){
                            self.$data.antecedente = '('+solicitud.orden_compra.tipo_nombre+') '+solicitud.orden_compra.numero_folio_format;
                            if(solicitud.orden_compra.referencia!=""){
                                self.$data.antecedente = self.$data.antecedente+' ('+solicitud.orden_compra.referencia+')';
                            }else{
                                self.$data.antecedente = self.$data.antecedente+'---';
                            }
                        }else{
                            self.$data.antecedente = '';
                        }

                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            numero_folio: '# ' + solicitud.numero_folio,
                            rubro: self.$data.rubro,
                            antecedente: self.$data.antecedente,
                            monto: solicitud.monto_format,
                            beneficiario: solicitud.empresa.razon_social,
                            fecha_registro: solicitud.fecha_format,
                            observaciones: solicitud.observaciones,
                            buttons: $.extend({}, {
                                show: true,
                                edit: self.$root.can('editar_solicitud_pago_anticipado') ? true : true,
                                cancelar: self.$root.can('cancelar_solicitud_pago_anticipado') ? true : true,
                                id: solicitud.id
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