
import { ref } from 'vue';

const createTaskRequest = (task) => {
    return axios.post('/api/task', {'hosts': task.hosts})
    .then( response => response.data);
}

const fetchTaskRequest = (id) => {
    axios.get('/api/task/id')
    .then( response => response.data);
}

const useChecker = () => {
    const hosts = ref([]);
    const rawData = ref('');
    const task = ref({ hosts: ['1233', '12121']});

    const startTask = async () => {
        const result = await createTaskRequest(task.value);
        console.log(result.data);
        task.value = result.data;
    }

    const checkTaskStatus = () => {
    }

    const reloadTask = async (id) => {
        const result = await fetchTaskRequest(id);
        task.value = result.data;
    }

    const reloadTasksHistory = () => {
    }

    const setActiveTask = (id) => {
    }

    return {
        hosts,
        rawData,
        task,

        startTask,
        checkTaskStatus,
        reloadTask,
        reloadTasksHistory,
        setActiveTask,
    }
}

export {
    useChecker
}

