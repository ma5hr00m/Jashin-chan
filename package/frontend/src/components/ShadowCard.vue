<template>
    <div class="card" :style="{ width: cardWidth + 'px', height: cardHeight + 'px' }">
        <div class="body">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
    name: 'ShadowCard',
    props: {
        cardW: {
            type: Number,
            default: 100
        },
        cardH: {
            type: Number,
            default: 100
        }
    },
    computed: {
        cardWidth() {
            return this.cardW;
        },
        cardHeight() {
            return this.cardH;
            }
    }
};
</script>


<style scoped>
.card {
    position: relative;
    background-color: #363940;
    border-radius: 8px;
}
.body {
    display: flex;
    width: 100%;
    height: 100%;
}
</style>