import { ref } from 'vue';
import axios from 'axios';

const notifications = ref([]);
const unreadCount = ref(0);
let poller = null;

export function useNotifications() {
    const fetchUnreadCount = async () => {
        try {
            const response = await axios.get('/api/notifications/unread-count');
            unreadCount.value = response.data?.data?.count ?? 0;
        } catch (error) {
            // sessiz geç
        }
    };

    const fetchNotifications = async (params = {}) => {
        try {
            const response = await axios.get('/api/notifications', { params });
            notifications.value = response.data?.data?.data || [];
        } catch (error) {
            console.error('notification fetch error', error);
        }
    };

    const markRead = async (id) => {
        try {
            await axios.post(`/api/notifications/${id}/read`);
            notifications.value = notifications.value.map((n) =>
                n.id === id ? { ...n, is_read: true } : n
            );
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        } catch (error) {
            console.error('notification mark read error', error);
        }
    };

    const startPolling = (interval = 30000) => {
        if (poller) return;
        fetchUnreadCount();
        poller = setInterval(fetchUnreadCount, interval);
    };

    const stopPolling = () => {
        if (poller) {
            clearInterval(poller);
            poller = null;
        }
    };

    return {
        notifications,
        unreadCount,
        fetchUnreadCount,
        fetchNotifications,
        markRead,
        startPolling,
        stopPolling,
    };
}
