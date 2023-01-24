<template>
    <span>

        <button class="btn btn-app pull-right dropdown-toggle"
        type="button"
        id="dropdownMenuButton"
        data-toggle="dropdown"
        data-boundary="window"
        aria-haspopup="true"
        aria-expanded="false">
            <span><i class="fa fa-file-pdf"></i></span>
            Informes
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
        methods: {
            proveedorEmpresa(){
                var url = '/api/fiscal/cfd-sat/informe-rep-pendientes-proveedor-empresa/pdf?'+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Informe</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            proveedor(){
                var url = '/api/fiscal/cfd-sat/informe-rep-pendientes-proveedor/pdf?'+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Informe</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            empresa(){
                var url = '/api/fiscal/cfd-sat/informe-rep-pendientes-empresa/pdf?'+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Informe</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            empresaProveedor(){
                var url = '/api/fiscal/cfd-sat/informe-rep-pendientes-empresa-proveedor/pdf?'+'&access_token='+this.$session.get('jwt');
                $(this.$refs.body).html('<iframe src="'+url+'"  frameborder="0" height="100%" width="100%">Informe</iframe>');
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
        }
    }
</script>
