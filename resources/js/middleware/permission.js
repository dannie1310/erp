export default function permission({ next, router }) {
    let permisos = router.app.$session.get('permisos');

    if (permisos) {
        if (Array.isArray(router.currentRoute.meta.permission)) {
            let result = false;
            router.currentRoute.meta.permission.forEach(perm => {
                let search = permisos.find(p => {
                    return p.name == perm;
                });
                if (search) {
                    result = true;
                }
            });
            return result ? next() : router.go(-1);
        }  else {
            return permisos.find(perm => {
                return perm.name == router.currentRoute.meta.permission ? next() : router.go(-1);
            })
        }
    }
    return router.go(-1);
}