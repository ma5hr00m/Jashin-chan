<script>
import RouterDock from './components/RouterDock.vue';

export default {
	name: 'App',
	components: {
		RouterDock,
	}
}
</script>

<template>
	<RouterDock />
	<main class="relative w-full h-full">
		<router-view></router-view>
	</main>
</template>