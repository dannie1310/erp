<template>
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"
            v-for="(breadcrumb, idx) in breadcrumbList"
            :key="idx"
            :class="{'active': !!breadcrumb.link}">
            <router-link v-if="breadcrumb.link" :to="breadcrumb.link"><span v-if="breadcrumb.name == 'INICIO'">
                    <i class="fa fa-home"></i>
                </span>
                <span v-else>
                    {{ breadcrumb.name }}
                </span></router-link>
            <span v-else>
                <span v-if="breadcrumb.name == 'INICIO'">
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
            routeTo (pRouteTo) {
                if (this.breadcrumbList[pRouteTo].link) this.$router.push(this.breadcrumbList[pRouteTo].link)
            },
            updateList () { this.breadcrumbList = this.$route.meta.breadcrumb }
        }
    }
</script>