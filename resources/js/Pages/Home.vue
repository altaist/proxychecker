<template>
<div v-if="viewState==1">
    <div>
        <textarea v-model="checkerManager.rawData"></textarea>
    </div>
    <div>
        <input type="submit" @click="onClick"></input>
    </div>
    <div>{{ checkerManager.task }}</div>


</div>
<div v-if="viewState==2">
    {{ checkerManager.task }}rrr
</div>
</template>

<script setup>
import { ref } from 'vue';
import { useChecker } from '../composables/proxycheck.js';

const viewState = ref(1);
const checkerManager = useChecker();


const onClick = () => {
    checkerManager.startTask();
    //viewState.value = 2;

}

</script>
