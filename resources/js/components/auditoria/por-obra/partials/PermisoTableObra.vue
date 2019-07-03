<template>
    <div class="card">
        <div class="card-header border-transparent">
            <h3 class="card-title">Información cantidad de permisos asignados a usuario por obras</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table m-0">
                    <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Nombre de Usuario</th>
                        <th>Ubicación</th>
                        <th>Departamento</th>
                        <th>Cantidad de Permisos</th>
                        <th>Cantidad de Proyectos</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Call of Duty IV</td>
                        <td>Call of Duty IV</td>
                        <td>Call of Duty IV</td>
                        <td>Call of Duty IV</td>
                        <td>Call of Duty IV</td>
                        <td>Call of Duty IV</td>
                        <td><span class="badge badge-success">Shipped</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
        </div>
        <!-- /.card-body -->
    </div>
</template>

<script>
    export default {
        name: "permiso-table-obra"
    }
</script>

<style scoped>

</style>