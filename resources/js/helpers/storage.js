/**
 * Safe wrapper for localStorage and sessionStorage
 */
export const storage = {
    get: (key, type = 'local') => {
        try {
            const storage = type === 'local' ? window.localStorage : window.sessionStorage;
            if (!storage) return null;
            return storage.getItem(key);
        } catch (e) {
            console.warn(`${type}Storage is not available`);
            return null;
        }
    },
    set: (key, value, type = 'local') => {
        try {
            const storage = type === 'local' ? window.localStorage : window.sessionStorage;
            if (!storage) return;
            storage.setItem(key, value);
        } catch (e) {
            console.warn(`Could not save to ${type}Storage`);
        }
    },
    remove: (key, type = 'local') => {
        try {
            const storage = type === 'local' ? window.localStorage : window.sessionStorage;
            if (!storage) return;
            storage.removeItem(key);
        } catch (e) {
            console.warn(`Could not remove from ${type}Storage`);
        }
    }
};
