<template>
    <div class="q-pa-md q-my-md">
        <q-tabs v-model="viewState" narrow-indicator dense align="justify" class="text-primary">
            <q-tab name="edit" label="Редактор" />
            <q-tab name="checking" label="Проверка" />
            <q-tab name="history" label="История" />
        </q-tabs>

        <q-tab-panels v-model="viewState" animated class="shadow-1 rounded-borders">
            <q-tab-panel name="edit">
                <div class="text-h6 q-mb-lg">Редактор адресов для проверки</div>
                <div class="q-my-md">
                    <q-input label="Список адресов в формате ip:port" v-model="checkerManager.rawData.value" type="textarea" filled />
                </div>
                <div v-if="checkerManager.rawData.value">
                    <q-btn label="Проверить" @click="onSaveClick"></q-btn>
                </div>
            </q-tab-panel>

            <q-tab-panel name="checking">
                <div class="text-h6 q-mb-lg">Результаты проверки</div>
                <div class="text-negative q-mb-md" v-if="checkerManager.error.value">{{ checkerManager.error.value }}</div>
                <div v-if="checkerManager.task.value.hosts.length>0">
                    <q-list bordered separator>
                    <q-item clickable v-ripple v-for="host in checkerManager.task.value.hosts">
                        <q-item-section>{{ host.address }}</q-item-section>
                        <q-item-section v-if="host.info">{{ host.info }}</q-item-section>
                        <q-item-section v-if="!host.info && checkerManager.needReload()">
                            <div><q-inner-loading :showing="true" dense label-class="text-teal"  /></div>
                        </q-item-section>
                        <q-item-section v-if="!host.info && !checkerManager.needReload()">
                            <div class="text-negative text-subtitle1 text-right">error</div>
                        </q-item-section>
                    </q-item>

                </q-list>
                </div>
                <div v-else>
                    <div class="q-ma-xl ">
                        <div class="text-h6"> Нет адресов для проверки</div>
                        <div>Переключитесь на вкладку Редактор и введите адреса</div>
                    </div>
                </div>

            </q-tab-panel>

            <q-tab-panel name="history">
                <div class="text-h6 q-mb-lg">История запросов</div>
                <div v-if="!checkerManager.tasksHistory.value.length">
                    <q-btn label="Загрузить" @click="onReloadHistory"></q-btn>
                </div>
                <div v-else>
                    <q-list bordered separator>
                        <q-item clickable v-ripple v-for="task in checkerManager.tasksHistory.value" @click="onHistoryTaskClicked(task)">
                            <q-item-section>{{ task.created_at }}</q-item-section>
                            <q-item-section >Адресов: {{ task.hosts.length }}</q-item-section>

                        </q-item>

                    </q-list>
                </div>

            </q-tab-panel>
        </q-tab-panels>


    </div>

</template>

<script setup>
import { ref } from 'vue';
import { useChecker } from '../composables/proxycheck.js';

// TODO разбить верстку на компоненты

const viewState = ref('edit');
const checkerManager = useChecker();

const onSaveClick = async () => {

    const result = await checkerManager.createTask();
    viewState.value = 'checking';
    checkerManager.monitoring();

}

const onReloadHistory = () => {
    checkerManager.reloadTasksHistory();
}

const onHistoryTaskClicked = (task) => {
    checkerManager.setActiveTask(task.id);
    viewState.value = 'checking';
}

</script>
