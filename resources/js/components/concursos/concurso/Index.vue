<template>
    <div class="row">
        <div class="col-12">
            <router-link  :to="{ name: 'concurso-create'}"  v-if="$root.can('registrar_concurso', true)" type="button" class="btn btn-app pull-right" title="Registrar">
                <i class="fa fa-plus"></i> Registrar
            </router-link>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                              <!--  <input type="text" class="form-control" placeholder="Buscar" v-model="search">-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <!--<datatable v-bind="$data" />-->
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        <router-view ></router-view>
    </div>
</template>

<script>
    import Registrar from './Create';
    export default {
        name: "concurso-index",
        components : {Registrar},
        data() {
            return {
            
            }
        },
    }
