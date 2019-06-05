<template>
    <span>
        <div class="card" id="configuracion-sistema">
            <div class="card-header">
                <h3 class="card-title">Habilitación de Sistemas</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label for="from">SISTEMAS HABILITADOS</label>
                                    <select multiple id="from" size="10" class="form-control">
                                        <option></option>
                                    </select>
                                </div>
                            </div>

                            <div class="container col-sm-2">
                                <div class="vertical-center align-content-center">
                                    <button class="btn col-xs-12 btn-default" title="Agregar" ><i class="fa fa-long-arrow-left"></i></button>
                                    <button class="btn col-xs-12 btn-default" title="Quitar" ><i class="fa fa-long-arrow-right"></i></button>
                                </div>
                            </div>

                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label for="to">SISTEMAS DISPONIBLES </label>
                                    <select multiple id="to" size="10" class="form-control" >
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                 </div>
            </div>


        </div>
        <div class="modal" ref="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalle de Asignación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Rol:</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Permisos a Asignar:</th>
                                    <td>
                                        <ul>
                                            <li ></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr >
                                    <th>Permisos a Desasignar:</th>
                                    <td>
                                        <ul>
                                            <li </li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" >
                            <span v-if="guardando">
                                <i class="fa fa-spin fa-spinner"></i>
                            </span>
                            <span v-else>
                                <i class="fa fa-save"></i> Guardar
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
    export default {
        name: "configuracion-sistema"
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
    .vue-treeselect__placeholder {
        color: #495057
    }

    .container {
        height: auto;
        position: relative;
        min-height: 50px;
    }

    .vertical-center {
        position: relative;
        top: 50%;
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>