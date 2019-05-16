export default function auth({ next, router }) {
    if (!router.app.$session.exists()) {
        router.app.$store.commit('auth/logout');
        router.app.$session.destroy();
        return router.push({name: 'login'});
    }
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + router.app.$session.get('jwt');

    if (router.app.$session.get('db') != undefined && router.app.$session.get('id_obra') != undefined) {
        window.axios.defaults.headers.common['db'] = router.app.$session.get('db');
        window.axios.defaults.headers.common['idobra'] = router.app.$session.get('id_obra');
    }

    if (router.app.$session.get('db') != undefined && router.app.$session.get('id_obra') != undefined) {
        $.ajaxSetup({
            headers: {
                'Authorization': 'Bearer ' + router.app.$session.get('jwt'),
                'db': router.app.$session.get('db'),
                'id_obra': router.app.$session.get('id_obra')
            }
        });
    } else {
        $.ajaxSetup({
            headers: {
                'Authorization': 'Bearer ' + router.app.$session.get('jwt'),
            }
        });
    }

    return next();
}