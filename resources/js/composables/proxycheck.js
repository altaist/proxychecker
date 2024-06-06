
import { ref } from 'vue';

const MAX_CHECKING_ATTEMPS = 3;
const CHECKING_TIMEOUT = 1000;

const createTaskRequest = (task) => {
    return axios.post('/api/task', { 'hosts': task.hosts })
        .then(response => response.data);
}

const fetchTaskRequest = (id) => {
    return axios.get('/api/task/' + id)
        .then(response => response.data);
}

const fetchTasksHistoryRequest = () => {
    return axios.get('/api/task')
        .then(response => response.data);
}

const useChecker = () => {
    const hosts = ref([]);
    const rawData = ref("85.214.56.195:5180\n27.147.28.73:8080");
    const task = ref({ hosts: [] });
    const tasksHistory = ref([]);
    const loading = ref(false);
    const checkingMode = ref(false);
    const message = ref('');
    const error = ref('');

    let  attempsCounter = 0;

    const showError = (str) => {
        error.value = str;
        console.log(str);
    }
    const clearError = () => {
        error.value = '';
    }

    const reset = () => {
        clearError();
        attempsCounter = 0;
    }

    const parseRawData = () => {
        const lines = rawData.value.split('\n');
        if (!lines) {
            return;
        }
        const hosts = [];
        const wrongLines = [];
        lines.forEach(item => {
            if (item.indexOf(':') >= 0) {
                // TODO: check ip and port format
                hosts.push(item);
            } else {
                wrongLines.push(item);
            }
        });

        task.value.hosts = hosts;
        rawData.value = wrongLines.join("\n");
    }

    const createTask = async () => {
        clearError();
        parseRawData();
        return startTask();
    }
    const startTask = async () => {
        if (!task.value.hosts) {
            showError('Wrong input data');
        }
        try {
            loading.value = true;
            const result = await createTaskRequest(task.value);
            task.value = result.data;
            checkingMode.value = true;
        } catch (error) {
            showError(error?.response?.data?.message || 'Server error');
        } finally {
            loading.value = false;
        }
    }

    const checkTaskStatus = () => {
    }

    const needReload = () => {
        if (checkingMode.value && attempsCounter < MAX_CHECKING_ATTEMPS) return true;
        checkingMode.value = false;
        return false;
    }

    const reloadActiveTask = () => {
        return reloadTask(task.value.id);
    }

    const reloadTask = async (id) => {
        attempsCounter++;
        if(!id) return;
        const result = await fetchTaskRequest(id);
        task.value = result.data;
    }

    const reloadTasksHistory = async () => {
        const result = await fetchTasksHistoryRequest(task.value.id);
        tasksHistory.value = result.data;
    }

    const setActiveTask = (id) => {
        reset();
        reloadTask(id)
    }

    const monitoring = () => {
        if (!needReload()) return;
        reloadActiveTask();
        setTimeout(() => {
            monitoring()
        }, CHECKING_TIMEOUT)

    }

    return {
        hosts,
        rawData,
        task,
        tasksHistory,
        loading,
        error,
        message,

        createTask,
        checkTaskStatus,
        reloadTask,
        reloadActiveTask,
        reloadTasksHistory,
        setActiveTask,
        needReload,
        monitoring
    }
}

export {
    useChecker
}

