<template>
    <span>
        <div v-if="currentUser" class="wrapper">
            <app-header/>
            <app-sidebar/>
            <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>{{ this.$router.currentRoute.meta.title }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <app-breadcrumb/>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <router-view></router-view>
                <!-- Default box -->
                <!--<div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Title</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                <i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                                <i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        Start creating your amazing application!
                    </div>
                    &lt;!&ndash; /.card-body &ndash;&gt;
                    <div class="card-footer">
                        Footer
                    </div>
                    &lt;!&ndash; /.card-footer&ndash;&gt;
                </div>-->
                <!-- /.card -->

            </section>
            <!-- /.content -->
        </div>
            <!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 3.0.0-alpha
            </div>
            <strong>Copyright &copy; 2014-2018 <a href="http://adminlte.io">AdminLTE.io</a>.</strong> All rights
            reserved.
        </footer>

            <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
            <!-- /.control-sidebar -->
    </div>
        <div v-else>
            <router-view></router-view>
        </div>
    </span>
</template>

<script>
    import AppHeader from "./pages/partials/Header";
    import AppSidebar from "./pages/partials/Sidebar";
    import AppBreadcrumb from "./pages/partials/Breadcrumb";
    export default {
        name: 'main-App',
        components: {AppBreadcrumb, AppSidebar, AppHeader},
        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            }
        }
    }
</script>