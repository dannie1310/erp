export function getLoggedinUser(){

    const session = localStorage.getItem('vue-session-key')

    if(!session){
        return null
    }

    return JSON.parse(session).user;
}

export function getObra() {
    const session = localStorage.getItem('vue-session-key')

    if(!session){
        return null
    }

    return JSON.parse(session).obra;
}

export function getEmpresa() {
    const session = localStorage.getItem('vue-session-key')

    if(!session){
        return null
    }

    return JSON.parse(session).empresa;
}

export function getPermisos() {
    const session = localStorage.getItem('vue-session-key')

    if(!session){
        return null
    }

    return JSON.parse(session).permisos;
}

export function getPermisosGenerales() {
    const session = localStorage.getItem('vue-session-key')

    if(!session){
        return null
    }

    return JSON.parse(session).permisos_generales;
}
