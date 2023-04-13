<template>
    <div class="fixed left-5 top-1/2 flex-col transform -translate-y-1/2 w-max h-min border-2 border-gray1 text-black rounded-md p-5">
        <div>
            <router-link to="/404">404</router-link>
        </div>
        <div>
            <router-link to="/">home</router-link>
        </div>
        <div>
            <router-link to="/login">login</router-link>
        </div>
        <div>
            <router-link to="/chatroom">chatroom</router-link>
        </div>
    </div>
</template>