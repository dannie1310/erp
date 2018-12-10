export function getLoggedinUser(){

    const userStr = JSON.parse(localStorage.getItem('vue-session-key')).user;

    if(!userStr){
        return null
    }

    return userStr;
}