<template>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"
            v-for="(breadcrumb, idx) in breadcrumbList"
            :key="idx"
            :class="{'active': !!breadcrumb.link}">
            <router-link v-if="breadcrumb.link" :to="breadcrumb.link">
                <span v-if="!breadcrumb.parent">
                    <i class="fa fa-home"></i>
                </span>
                <span v-else>
                    {{ breadcrumb.name }}
                </span>
            </router-link>
            <span v-else>
                <span v-if="!breadcrumb.parent">
                    <i class="fa fa-home"></i>
                </span>
                <span v-else>
                    {{ breadcrumb.name }}
                </span>
            </span>
        </li>
    </ol>
</template>

<script>
    export default {
        name: 'app-breadcrumb',
        data () {
            return {
                breadcrumbList: []
            }
        },
        mounted () { this.updateList() },
        watch: { '$route' () { this.updateList() } },
        methods: {
            updateList () {
                this.breadcrumbList = [];
                if(this.$route.name != null) {
                    this.push(this.$route)
                }

            },

            push(route) {
                if(route.meta.breadcrumb.parent) {
                    this.push(this.$router.resolve({name: route.meta.breadcrumb.parent}).resolved);
                    this.breadcrumbList.push({...route.meta.breadcrumb, link: route == this.$route ? null : route.path});
                } else {
                    this.breadcrumbList.push({...route.meta.breadcrumb, link: route == this.$route ? null : route.path});
                }
            }
        }
    }
</script>