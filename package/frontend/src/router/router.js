import { createRouter, createWebHistory } from "vue-router";

const routes = [{
    path: "/login",
    name: "login",
    component: () =>
        import ("../modules/LoginPage.vue")
}, {
    path: "/",
    name: "home",
    component: () =>
        import ("../modules/HomePage.vue")
}, {
    path: "/chatroom",
    name: "chatroom",
    component: () =>
        import ("../modules/ChatRoomPage.vue")
}, {
    path: "/:pathMatch(.*)*",
    name: "notFound",
    component: () =>
        import ("../modules/NotFoundPage.vue")
}]

const Router = createRouter({
    history: createWebHistory(),
    routes,
})

export default Router;