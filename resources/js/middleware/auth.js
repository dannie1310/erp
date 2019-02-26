export default function auth({ next, router }) {
    if (! router.app.$session.exists()) {
        router.app.$store.commit('auth/logout');
        router.app.$session.destroy();
        return router.push({ name: 'login' });
    }
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + router.app.$session.get('jwt');
    $.ajaxSetup({
        headers: { 'Authorization': 'Bearer ' + router.app.$session.get('jwt') }
    });
    return next();
}