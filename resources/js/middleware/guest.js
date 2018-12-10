export default function guest({ next, router }) {

    if(! router.app.$session.exists()) {
        return next();
    }

    router.push({name: 'dashboard'});
}