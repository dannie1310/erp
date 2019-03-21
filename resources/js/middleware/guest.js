export default function guest({ next, router }) {
    return router.app.$session.exists() ? router.push({name: 'home'}) : next();
}