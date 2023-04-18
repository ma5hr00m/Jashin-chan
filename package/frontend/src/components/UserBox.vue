<template>
    <div class="relative flex w-full h-10 border-2 border-gray-600 mb-4 rounded-lg">
        <div class="absolute left-30px top-1/2 rounded-1 h-2 w-2 bg-green-400 transform -translate-y-1/2"></div>
        <span class="absolute w-120px truncate left-50px text-gray-300 text-16px top-1/2 transform -translate-y-1/2">{{ user }}</span>
    </div>
</template>

<script>
export default {
    props: {
        user: {
            type: String,
            required: true
        }
    }
}
</script>
