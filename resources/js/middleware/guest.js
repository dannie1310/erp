export default function guest({ next, router }) {

    if(! router.app.$session.exists()) {
        return next();
    }

    return router.push({name: 'home'});
}