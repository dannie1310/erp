<template>
    <span>

        <button class="btn btn-app pull-right dropdown-toggle"
                :disabled="cargando_padre"
        type="button"
        id="dropdownMenuButton"
        data-toggle="dropdown"
        data-boundary="window"
        aria-haspopup="true"
        aria-expanded="false">
            <span><i class="fa fa-file-pdf"></i></span>
            Informes PDF
        </button>
        <div class="dropdown-menu">
            <button @click="proveedor" type="button" class="btn btn-sm  dropdown-item" title="Desglosado por proveedor" >
                <i class="fa fa-user"></i>Por proveedor
            </button>
            <button @click="proveedorEmpresa" type="button" class="btn btn-sm  dropdown-item" title="Desglosado por proveedor y empresa" >
                <i class="fa fa-users"></i>Por proveedor y empresa
            </button>
            <button @click="empresa" type="button" class="btn btn-sm  dropdown-item" title="Desglosado por proveedor y empresa" >
                <i class="fa fa-user"></i>Por empresa
            </button>
            <button @click="empresaProveedor" type="button" class="btn btn-sm  dropdown-item" title="Desglosado por proveedor y empresa" >
                <i class="fa fa-users"></i>Por empresa y proveedor
            </button>
        </div>


        <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal">
            <div class="modal-dialog modal-lg" id="mdialTamanio">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Informe</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                    </div>
                    <div class="modal-body modal-lg" style="height: 800px" ref="body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        props: ['query','cargando_padre'],
        data(){
            return{
                filtros: '?',
            }
        },
        methods: {
            armaCadenaFiltros(){
                console.log(this.query);


                if (typeof this.query.rfc_proveedor !== 'undefined') {
                    this.filtros = this.filtros + 'rfc_proveedor='+ this.query.rfc_proveedor + '&';
                }
                if (typeof this.query.proveedor !== 'undefined') {
                    this.filtros = this.filtros + 'proveedor='+ this.query.proveedor + '&';
                }
                if (typeof this.query.cantidad_cfdi !== 'undefined') {
                    this.filtros = this.filtros + 'cantidad_cfdi='+ this.query.cantidad_cfdi + '&';
                }
                if (typeof this.query.total_cfdi !== 'undefined') {
                    this.filtros = this.filtros + 'total_cfdi='+ this.query.total_cfdi + '&';
                }
                if (typeof this.query.total_rep !== 'undefined') {
                    this.filtros = this.filtros + 'total_rep='+ this.query.total_rep + '&';
                }

                if (typeof this.query.pendiente_rep !== 'undefined') {
                    this.filtros = this.filtros + 'pendiente_rep='+ this.query.pendiente_rep + '&';
                }
                if (typeof this.query.ultima_ubicacion_sao !== 'undefined') {
                    this.filtros = this.filtros + 'ultima_ubicacion_sao='+ this.query.ultima_ubicacion_sao + '&';
                }
                if (typeof this.query.ultima_ubicacion_contabilidad !== 'undefined') {
                    this.filtros = this.filtros + 'ultima_ubicacion_contabilidad='+ this.query.ultima_ubicacion_contabilidad + '&';
                }
                this.filtros = this.filtros + 'es_hermes='+ this.query.es_hermes + '&';
                this.filtros = this.filtros + 'no_hermes='+ this.query.no_hermes + '&';
                this.filtros = this.filtros + 'con_contactos='+ this.query.con_contactos + '&';
                this.filtros = this.filtros + 'sin_contactos='+ this.query.sin_contactos + '&';
            },
            proveedorEmpresa(){
                this.armaCadenaFiltros();

                var url = '/api/fiscal/cfd-sat/informe-rep-pendientes-proveedor-empresa/pdf'+this.filtros+'access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Informe</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            proveedor(){
                this.armaCadenaFiltros();
                var url = '/api/fiscal/cfd-sat/informe-rep-pendientes-proveedor/pdf'+this.filtros+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Informe</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            empresa(){
                this.armaCadenaFiltros();
                var url = '/api/fiscal/cfd-sat/informe-rep-pendientes-empresa/pdf'+this.filtros+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Informe</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            empresaProveedor(){
                this.armaCadenaFiltros();
                var url = '/api/fiscal/cfd-sat/informe-rep-pendientes-empresa-proveedor/pdf'+this.filtros+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Informe</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
        }
    }
</script>
